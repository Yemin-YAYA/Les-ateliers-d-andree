<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use App\Service\BreadcrumbService;  //S'assurer que ce service est bien importé

class ContactController extends AbstractController
{
    private $breadcrumbService;

    // Injection du service via le constructeur
    public function __construct(BreadcrumbService $breadcrumbService)
    {
        $this->breadcrumbService = $breadcrumbService;
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Ajout du breadcrumb
        $this->breadcrumbService->addBreadcrumb('Accueil', $this->generateUrl('app_post'));
        $this->breadcrumbService->addBreadcrumb('Contact', $this->generateUrl('app_contact'));

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request); /*permet de recuperer le formulaire*/

        if ($form->isSubmitted() && $form->isValid()) {

            // Validation reCAPTCHA
            $recaptchaResponse = $request->request->get('g-recaptcha-response');
          
            if ($recaptchaResponse) {
                $client = HttpClient::create();
                $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                    'body' => [
                        'secret' => '#', // Remplace avec ta vraie clé secrète
                        'response' => $recaptchaResponse,
                    ],
                ]);

                $data = $response->toArray();
              

                if (!$data['success']) {
                    $this->addFlash('error', 'La vérification reCAPTCHA a échoué.');
                    return $this->redirectToRoute('app_contact');
                }
            } else {
                $this->addFlash('error', 'reCAPTCHA non rempli.');
                return $this->redirectToRoute('app_contact');
            }

            // Récupérer la raison du contact
            $raison = $contact->getRaison();

            // Préparer le contenu de l'e-mail avec la raison du contact
            $emailContent = sprintf(
                '<p><strong>Nom:</strong> %s</p>
                <p><strong>Email:</strong> %s</p>
                <p><strong>Motif du contact:</strong> %s</p>
                <p><strong>Message:</strong> %s</p>',
                htmlspecialchars($contact->getName()),  // Nom de l'expéditeur
                htmlspecialchars($contact->getEmail()), // Email de l'expéditeur
                htmlspecialchars($raison),              // Raison du contact
                nl2br(htmlspecialchars($contact->getMessage()))  // Le message
            );

            // Envoyer l'e-mail
            $email = (new Email())
                ->from('lesateliersdandree79@gmail.com') /*obligation d'avoir la même adresse smtp dans le from et le to pour que ça fonctionne*/
                ->replyTo($contact->getEmail()) /*recupere l'email de l'expediteur*/
                ->to('lesateliersdandree79@gmail.com') /*Email de reception*/
                ->subject(sprintf('Raison du contact: %s', $contact->getRaison()))
                ->html($emailContent); /*Le contenu de l'email avec la raison et le message*/

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a été envoyé avec succès.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/contact.html.twig', [
            'formContact' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbService->getBreadcrumbs(), // Passer les breadcrumbs à la vue
        ]);
    }
}
