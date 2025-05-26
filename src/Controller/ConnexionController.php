<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'connexion')]
    public function index(): Response
    {
        return $this->render('connexion.html.twig');
    }


    #[Route('/traitement-connexion', name: 'traitement_connexion', methods: ['POST'])]
    public function traitementConnexion(Request $request, EntityManagerInterface $em, SessionInterface $session, UserPasswordHasherInterface $passwordHasher): Response
    {
        
        $email = $request->request->get('mail-connexion');
        $mdp = $request->request->get('mdp-connexion');


        $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        if (!$utilisateur) {
            $this->addFlash('danger', 'Mail invalide.');
            return $this->redirectToRoute('connexion');
        }
        

        if (!$passwordHasher->isPasswordValid($utilisateur, $mdp)) {
            $this->addFlash('danger', 'Mot de passe invalide');
            return $this->redirectToRoute('connexion');
        }
        


        $session->set('utilisateur', [
            'id' => $utilisateur->getId(),
            'nom' => $utilisateur->getNom(),
            'email' => $utilisateur->getMail(),
        ]);

        return $this->redirectToRoute('lesLivrets');
    }

    #[Route('/traitement-inscription', name: 'traitement-inscription')]
    public function traitementInscription(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = new Utilisateur();

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom-inscription');
            $mail = $request->request->get('mail-inscription');
            $mdp = $request->request->get('mdp-inscription');
            $confirmMdp = $request->request->get('mdpConfirm-inscription');

            if ($mdp !== $confirmMdp) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('connexion');
            }

            $verif = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/';
            if (!preg_match($verif, $mdp)) {
                $this->addFlash('error', 'Le mot de passe doit contenir au minimum 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
                return $this->redirectToRoute('connexion');
            }

            $utilisateur->setNom($nom)
                        ->setMail($mail)
                        ->setMdp(password_hash($mdp, PASSWORD_BCRYPT));

            $em->persist($utilisateur);
            $em->flush();
            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('connexion'); 
        }

        return $this->render('index.html.twig');
    }

    #[Route('/deconnexion', name: 'deconnexion')]
    public function deconnexion(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('connexion');
    }
}
