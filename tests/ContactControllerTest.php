<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');
        // Vérifie que la réponse est réussie
        $this->assertResponseIsSuccessful();
        
        // Vérifie que le titre h1 contient le texte "Contactez moi"
        $this->assertSelectorTextContains('h1', 'Contactez moi');

        // Soumission du formulaire
        $form = $crawler->selectButton('Envoyer')->form();
        $form['contact[email]'] = 'test@example.com';
        $form['contact[message]'] = 'Ceci est un message de test.';
       $client->submit($form);
        // Vérifie la redirection après la soumission du formulaire
        $this->assertResponseRedirects('/contact');
        $client->followRedirect();
        // Vérifie que le message de succès est présent après redirection
        $this->assertSelectorExists('.alert-success');
        $this->assertSelectorTextContains('.alert-success', 'Votre message a été envoyé avec succès.');
    }

  }
