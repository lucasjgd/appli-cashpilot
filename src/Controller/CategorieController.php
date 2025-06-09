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

final class CategorieController extends AbstractController
{

    #[Route('/lesLivrets/{id}/detail/ajoutCategLivret', name: 'ajoutCategLivret', methods: ['POST'])]
    public function ajoutCateg(int $id, Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $categorieId = $request->request->get('categorie_id');
        $budget = $request->request->get('budget');

        if (!$categorieId || !$budget) {
            $this->addFlash('danger', 'Données invalides.');
            return $this->redirectToRoute('detailLivret', ['id' => $id]);
        }

        $livret = $em->getRepository(Livret::class)->find($id);
        $selectedCategorie = $em->getRepository(Categorie::class)->find($categorieId);

        if (!$livret || !$selectedCategorie) {
            $this->addFlash('danger', 'Livret ou catégorie introuvable.');
            return $this->redirectToRoute('detailLivret', ['id' => $id]);
        }

        $avoirActif = $em->getRepository(Avoir::class)->findOneBy([
            'livret' => $livret,
            'categorie' => $selectedCategorie,
            'actif' => true
        ]);

        if ($avoirActif) {
            $this->addFlash('danger', 'Cette catégorie est déjà présente dans ce livret.');
            return $this->redirectToRoute('detailLivret', ['id' => $id]);
        }

        $avoirsDesactives = $em->getRepository(Avoir::class)->findBy([
            'livret' => $livret,
            'actif' => false
        ]);

        foreach ($avoirsDesactives as $avoirDesactive) {
            $categ = $avoirDesactive->getCategorie();
            if (strtolower($categ->getLibelle()) === strtolower($selectedCategorie->getLibelle())) {
                $nouvelleCategorie = new Categorie();
                $nouvelleCategorie->setLibelle($categ->getLibelle());
                $em->persist($nouvelleCategorie);

                $nouvelAvoir = new Avoir();
                $nouvelAvoir->setLivret($livret);
                $nouvelAvoir->setCategorie($nouvelleCategorie);
                $nouvelAvoir->setBudgetMaxCateg((float) $budget);
                $nouvelAvoir->setActif(true);

                $em->persist($nouvelAvoir);
                $em->flush();

                $this->addFlash('success', 'Nouvelle catégorie ajoutée (même nom que l\'ancienne désactivée).');
                return $this->redirectToRoute('detailLivret', ['id' => $id]);
            }
        }

        $avoir = new Avoir();
        $avoir->setLivret($livret);
        $avoir->setCategorie($selectedCategorie);
        $avoir->setBudgetMaxCateg((float) $budget);
        $avoir->setActif(true);

        $em->persist($avoir);
        $em->flush();

        $this->addFlash('success', 'Catégorie ajoutée au livret avec succès !');
        return $this->redirectToRoute('detailLivret', ['id' => $id]);
    }

    #[Route('/modifierCategorie/{idLivret}/{idCateg}', name: 'modifierCategorie', methods: ['POST'])]
    public function modifierCategorie(int $idLivret, int $idCateg, Request $request, EntityManagerInterface $em): Response
    {
        $avoir = $em->getRepository(Avoir::class)->findOneBy([
            'livret' => $idLivret,
            'categorie' => $idCateg
        ]);

        if (!$avoir) {
            $this->addFlash('danger', "Catégorie non trouvée pour ce livret.");
            return $this->redirectToRoute('detailLivret', ['id' => $idLivret]);
        }

        $budget = $request->request->get('modifBudget');
        if (!$budget || $budget < 0) {
            $this->addFlash('danger', "Budget invalide.");
            return $this->redirectToRoute('detailLivret', ['id' => $idLivret]);
        }

        $avoir->setBudgetMaxCateg($budget);
        $em->flush();

        $this->addFlash('success', "Budget de la catégorie modifié avec succès !");
        return $this->redirectToRoute('detailLivret', ['id' => $idLivret]);
    }

    #[Route('/lesLivrets/{livretId}/desactiverCategorie/{categId}', name: 'desactiverCateg', methods: ['GET'])]
    public function desactiverCateg(int $livretId, int $categId, EntityManagerInterface $em): Response
    {

        $avoir = $em->getRepository(Avoir::class)->findOneBy([
            'livret' => $livretId,
            'categorie' => $categId,
        ]);

        if (!$avoir) {
            $this->addFlash('danger', 'Catégorie introuvable dans ce livret.');
            return $this->redirectToRoute('detailLivret', ['id' => $livretId]);
        }

        $avoir->setActif(false);
        $em->flush();

        $this->addFlash('success', 'Catégorie supprimé avec succès à cette période.');
        return $this->redirectToRoute('detailLivret', ['id' => $livretId]);

    }
}
