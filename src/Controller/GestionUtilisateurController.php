<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use App\Entity\Admin;
use App\Entity\Client;
use App\Entity\Livret;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Knp\Component\Pager\PaginatorInterface;


final class GestionUtilisateurController extends AbstractController
{
    #[Route('/gestion_utilisateur', name: 'gestion_utilisateur')]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $pagination, Request $request): Response
    {
        $utilisateurs = $entityManager->getRepository(Utilisateur::class)->orderByRole();

        $utilisateursPagination = $pagination->paginate(
            $utilisateurs,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('gestion_utilisateur.html.twig', [
            'utilisateurs' => $utilisateursPagination,
        ]);
    }

    #[Route('/gestion_utilisateur/ajout', name: 'gestion_utilisateur_ajout', methods: ['POST'])]
    public function ajoutUtilisateur(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hash): Response
    {
        $nom = $request->request->get('nom');
        $mdp = $request->request->get('mdp');
        $mail = $request->request->get('mail');
        $role = $request->request->get('role');

        $verif = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/';

        if (!preg_match($verif, $mdp)) {
            $this->addFlash('error', "Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
            return $this->redirectToRoute('gestion_utilisateur');
        }

        if ($role === "admin") {
            $utilisateur = new Admin();
        } else {
            $utilisateur = new Client();
        }

        $utilisateur->setNom($nom);
        $utilisateur->setMail($mail);
        $mdpHash = $hash->hashPassword($utilisateur, $mdp);
        $utilisateur->setMdp($mdpHash);

        $entityManager->persist($utilisateur);
        $entityManager->flush();

        $this->addFlash('success', "Utilisateur ajouté avec succès.");
        return $this->redirectToRoute('gestion_utilisateur');
    }

    #[Route('/gestion_utilisateur/modif/{id}', name: 'gestion_utilisateur_modif', methods: ['POST'])]
    public function modifUtilisateur(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hash
    ): Response {
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id);
        if (!$utilisateur) {
            $this->addFlash('error', "Utilisateur non trouvé.");
            return $this->redirectToRoute('gestion_utilisateur');
        }

        $ancienMdp = $utilisateur->getMdp();

        $verif = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/';

        $nom = $request->request->get('modif-nom');
        $mail = $request->request->get('modif-mail');
        $mdp = $request->request->get('modif-mdp');
        $role = $request->request->get('modif-role');

        if ($mdp && !preg_match($verif, $mdp)) {
            $this->addFlash('error', "Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
            return $this->redirectToRoute('gestion_utilisateur');
        }


        if ($utilisateur::class !== 'App\Entity\\' . ucfirst($role)) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            switch ($role) {
                case "admin":
                    $nouvUtilisateur = new Admin();
                    break;
                default:
                    $nouvUtilisateur = new Client();
            }

            $utilisateur = $nouvUtilisateur;
        }

        $utilisateur->setNom($nom);
        $utilisateur->setMail($mail);

        if ($mdp) {
            $mdpHash = $hash->hashPassword($utilisateur, $mdp);
            $utilisateur->setMdp($mdpHash);
        } else {
            $utilisateur->setMdp($ancienMdp);
        }

        $entityManager->persist($utilisateur);
        $entityManager->flush();

        $this->addFlash('success', "Utilisateur modifié avec succès.");
        return $this->redirectToRoute('gestion_utilisateur');
    }



    #[Route('/gestion_utilisateur/suppr/{id}', name: 'gestion_utilisateur_suppr', methods: ['POST'])]
    public function supprUtilisateur(int $id, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$utilisateur) {
            $this->addFlash('error', "Utilisateur non trouvé.");
            return $this->redirectToRoute('gestion_utilisateur');
        }

        $nbLivret = $entityManager->getRepository(Livret::class)->nbrLivret($utilisateur);

        if ($nbLivret > 0) {
            $this->addFlash('error', "Impossible de supprimer l'utilisateur, au moins 1 livret est associé à celui-ci.");
            return $this->redirectToRoute('gestion_utilisateur');

        }

        $entityManager->remove($utilisateur);
        $entityManager->flush();

        $this->addFlash('success', "Utilisateur supprimé avec succès.");
        return $this->redirectToRoute('gestion_utilisateur');
    }

}
