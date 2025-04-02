<?php
namespace App\Controller\Admin;
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Rubrik;
use App\Entity\Comment;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
class DashboardController extends AbstractDashboardController
{
    protected $userRepository;
    //mettre en place le contructeur
    public function __construct(UsersRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
      //Définir le rôle minimum à avoir pour accéder au DASHBOARD
      if ($this->isGranted('ROLE_MODO') || $this->isGranted('ROLE_EDITOR')) {
        return $this->render('admin/dashboard.html.twig');
    } else {
        return $this->redirectToRoute('app_post');
    }
}
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle("Les Ateliers D'Andrée");
    }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retourner sur le site', 'fa-solid fa-arrow-rotate-left', 'app_post');
       
        if($this->isGranted('ROLE_ADMIN')){
            yield MenuItem::linkToDashboard('Espace Administrateur', 'fa fa-home')->setPermission('ROLE_SUPER_ADMIN');
        }
        //LES AUTRES ROLE
        if($this->isGranted('ROLE_EDITOR')){
            yield MenuItem::section('Articles');
            yield MenuItem::subMenu('Articles', 'fa_sharp fa-solid fa-blog')->setSubItems([
                  MenuItem::linkToCrud('Creer un article', 'fas fa-newspaper', Post::class)->setAction(Crud::PAGE_NEW),
                  MenuItem::linkToCrud('Voir les articles', 'fas fa-eye', Post::class),
            ]);
        }
      
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Rubrique');
            yield MenuItem::subMenu('Rubrique', 'fa-solid fa-book-open-reader')->setSubItems([
                  MenuItem::linkToCrud('Creer une rubrique', 'fas fa-newspaper', Rubrik::class)->setAction(Crud::PAGE_NEW),
                  MenuItem::linkToCrud('Voir les rubriques', 'fas fa-eye', Rubrik::class),
            ]);
        }
        if($this->isGranted('ROLE_SUPER_ADMIN')){
            yield MenuItem::section('Utilisateurs');
            yield MenuItem::subMenu('Utilisateurs', 'fa fa-user-circle')->setSubItems([
                  MenuItem::linkToCrud('Voir les uitilisateurs', 'fas fa-eye', User::class),
            ]);
         }
    }
   public function configureUserMenu(UserInterface $user): UserMenu
    {
        if(!$user instanceof User){
            throw new \Exception('Wrong user');
        }
        $avatar = 'divers/avatars/' . $user->getAvatar();
        return parent::configureUserMenu($user)
        ->setAvatarUrl($avatar);
    }
}
