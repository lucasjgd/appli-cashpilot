<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GestionCategorieController extends AbstractController
{
    #[Route('/gestion_categorie', name: 'gestion_categorie')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = $request->getSession()->get('utilisateur');
        if (!$utilisateur || strtolower($utilisateur['role']) !== 'admin') {
            return $this->redirectToRoute('lesLivrets');
        }
        
        $categories = $em->getRepository(Categorie::class)->categOrdreAsc();

        return $this->render('gestion_categorie.html.twig', [
            'categories' => $categories,
        ]);
    }


    #[Route('/gestion_categorie/ajout', name: 'gestion_categorie_ajout', methods: ['POST'])]
    public function ajoutCategorie(Request $request, EntityManagerInterface $em): Response
    {
        $libelle = $request->request->get('libelle');

        if (!$libelle) {
            $this->addFlash('error', 'Le libellé ne peut pas être vide.');
            return $this->redirectToRoute('gestion_categorie');
        }

        $libelle = ucfirst(strtolower($libelle));

        $categorie = new Categorie();
        $categorie->setLibelle($libelle);

        $em->persist($categorie);
        $em->flush();

        $this->addFlash('success', 'Catégorie ajoutée avec succès.');
        return $this->redirectToRoute('gestion_categorie');
    }


    #[Route('/gestion_categorie/modif/{id}', name: 'gestion_categorie_modif', methods: ['POST'])]
    public function modifierCategorie(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $categorie = $em->getRepository(Categorie::class)->find($id);
        if (!$categorie) {
            $this->addFlash('error', 'Catégorie non trouvée.');
            return $this->redirectToRoute('gestion_categorie');
        }

        $ancienLibelle = $categorie->getLibelle();
        $nouveauLibelle = $request->request->get('modif-libelle');

        $categoriesAvecMemeNom = $em->getRepository(Categorie::class)->findBy(['libelle' => $ancienLibelle]);

        foreach ($categoriesAvecMemeNom as $c) {
            $c->setLibelle($nouveauLibelle);
        }

        $em->flush();

        $this->addFlash('success', 'Catégorie modifiée avec succès.');
        return $this->redirectToRoute('gestion_categorie');
    }


    #[Route('/gestion_categorie/suppr/{id}', name: 'gestion_categorie_suppr', methods: ['POST'])]
    public function supprimerCategorie(int $id, EntityManagerInterface $em): Response
    {
        $categorie = $em->getRepository(Categorie::class)->find($id);
        if (!$categorie) {
            $this->addFlash('error', 'Catégorie non trouvée.');
            return $this->redirectToRoute('gestion_categorie');
        }

        if (count($categorie->getDepenses()) > 0) {
            $this->addFlash('error', 'Impossible de supprimer : des dépenses sont liées.');
            return $this->redirectToRoute('gestion_categorie');
        }

        $em->remove($categorie);
        $em->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès.');
        return $this->redirectToRoute('gestion_categorie');
    }
}
