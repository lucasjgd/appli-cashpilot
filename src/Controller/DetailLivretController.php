<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Livret;
use App\Entity\Avoir;
use App\Entity\Categorie;

final class DetailLivretController extends AbstractController
{
    #[Route('/lesLivrets/{id}/detail', name: 'detailLivret')]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $livret = $em->getRepository(Livret::class)->find($id);
        $categories = $em->getRepository(Categorie::class)->categOrdreAlpha();
        $avoirs = $livret->getAvoirs();

        if (!$livret) {
            throw $this->createNotFoundException('Livret non trouvé');
        }

        return $this->render('detailLivret.html.twig', [
            'livret' => $livret,
            'categories' => $categories,
            'avoirs' => $avoirs,
        ]);
    }

    #[Route('/lesLivrets/{id}/detail/ajoutCategLivret', name: 'ajoutCategLivret', methods: ['POST'])]
    public function categDansLivret(int $id, Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $sessionUtilisateur = $session->get('utilisateur');
        if (!$sessionUtilisateur) {
            $this->addFlash('danger', "Vous devez être connecté pour ajouter une catégorie au livret.");
            return $this->redirectToRoute('connexion');
        }

        $livret = $em->getRepository(Livret::class)->find($id);
        if (!$livret) {
            $this->addFlash('danger', 'Livret non trouvé.');
            return $this->redirectToRoute('detailLivret', ['id' => $id]);
        }

        $categorieId = $request->request->get('categorie_id');
        $budgetMax = $request->request->get('budget');

        $categorie = $em->getRepository(Categorie::class)->find($categorieId);
        if (!$categorie) {
            $this->addFlash('danger', 'Catégorie invalide.');
            return $this->redirectToRoute('detailLivret', ['id' => $id]);
        }

        $existant = $em->getRepository(Avoir::class)->findOneBy([
            'livret' => $livret,
            'categorie' => $categorie,
        ]);
        if ($existant) {
            $this->addFlash('warning', 'Cette catégorie est déjà ajoutée à ce livret.');
            return $this->redirectToRoute('detailLivret', ['id' => $id]);
        }

        $avoir = new Avoir();
        $avoir->setLivret($livret);
        $avoir->setCategorie($categorie);
        $avoir->setBudgetMaxCateg((float) $budgetMax);

        $em->persist($avoir);
        $em->flush();

        $this->addFlash('success', 'Catégorie ajoutée au livret avec succès !');

        return $this->redirectToRoute('detailLivret', ['id' => $id]);
    }

}
