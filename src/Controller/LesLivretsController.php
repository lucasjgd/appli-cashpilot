<?php

namespace App\Controller;

use App\Entity\Livret;
use App\Entity\Depense;
use App\Entity\Avoir;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class LesLivretsController extends AbstractController
{
    #[Route('/lesLivrets', name: 'lesLivrets')]
    public function index(EntityManagerInterface $em, SessionInterface $session): Response
    {
        $sessionUtilisateur = $session->get('utilisateur');
        $livrets = [];

        if ($sessionUtilisateur) {
            $utilisateur = $em->getRepository(Utilisateur::class)->find($sessionUtilisateur['id']);
            $livrets = $utilisateur?->getLivrets() ?? [];
        }

        return $this->render('lesLivrets.html.twig', [
            'livrets' => $livrets,
        ]);
    }

    #[Route('/ajouterLivret', name: 'ajouterLivret', methods: ['POST'])]
    public function ajouterLivret(Request $request, EntityManagerInterface $em, SessionInterface $session, LoggerInterface $logs): Response
    {
        $nomLivret = $request->request->get('nom_livret');

        $sessionUtilisateur = $session->get('utilisateur');
        if (!$sessionUtilisateur) {
            $this->addFlash('danger', "Vous devez être connecté pour ajouter un livret.");
            return $this->redirectToRoute('connexion');
        }

        $utilisateur = $em->getRepository(Utilisateur::class)->find($sessionUtilisateur['id']);
        if (!$utilisateur) {
            $this->addFlash('danger', "Utilisateur introuvable.");
            return $this->redirectToRoute('connexion');
        }

        $livret = new Livret();
        $livret->setNom($nomLivret);
        $livret->setUtilisateur($utilisateur);

        $em->persist($livret);
        $em->flush();

        $logs->info('Livret ajouté', [
            'date' => new \DateTime(),
            'utilisateur_id' => $utilisateur->getId(),
            'livret_id' => $livret->getId(),
        ]);

        $this->addFlash('success', "Livret ajouté avec succès !");
        return $this->redirectToRoute('lesLivrets');
    }

    #[Route('/modifierLivret/{id}', name: 'modifierLivret', methods: ['POST'])]
    public function modifierLivret(int $id, Request $request, EntityManagerInterface $em, LoggerInterface $logs): Response
    {
        $livret = $em->getRepository(Livret::class)->find($id);
        if (!$livret) {
            $this->addFlash('danger', "Livret non trouvé.");
            return $this->redirectToRoute('lesLivrets');
        }

        $nom = $request->request->get('modifNom');
        if (!$nom) {
            $this->addFlash('danger', "Nom invalide.");
            return $this->redirectToRoute('lesLivrets');
        }

        $livret->setNom($nom);
        $em->flush();

        $logs->info('Livret modifié', [
            'date' => new \DateTime(),
            'utilisateur_id' => $livret->getUtilisateur()->getId(),
            'livret_id' => $livret->getId(),
        ]);

        $this->addFlash('success', "Livret modifié avec succès !");
        return $this->redirectToRoute('lesLivrets');
    }
    #[Route('/supprimerLivret/{id}', name: 'supprimerLivret', methods: ['GET'])]
    public function supprimerLivret(int $id, EntityManagerInterface $em, LoggerInterface $logs): Response
    {
        $livret = $em->getRepository(Livret::class)->find($id);

        if (!$livret) {
            $this->addFlash('danger', "Livret non trouvé.");
            return $this->redirectToRoute('lesLivrets');
        } else {
            $depenses = $em->getRepository(Depense::class)->findBy(['livret' => $livret]);
            $avoirs = $em->getRepository(Avoir::class)->findBy(['livret' => $livret]);
            foreach ($depenses as $d) {
                $em->remove($d);
            }
            foreach ($avoirs as $a) {
                $em->remove($a);
            }
            $em->remove($livret);
        }
        $em->flush();

        $logs->info('Livret supprimé', [
            'date' => new \DateTime(),
            'utilisateur_id' => $livret->getUtilisateur()->getId(),
            'livret_id' => $livret->getId(),
        ]);

        $this->addFlash('success', "Livret supprimé avec succès. Dépenses et catégories associées de même.");
        return $this->redirectToRoute('lesLivrets');
    }
}
