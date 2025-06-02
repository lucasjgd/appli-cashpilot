<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Livret;
use App\Entity\Categorie;
use App\Entity\Depense;
use App\Entity\Recurrence;
use DateTime;

final class DepenseController extends AbstractController
{
    #[Route('/ajouterDepense', name: 'ajouterDepense', methods: ['POST'])]
    public function ajouterDepense(Request $request, EntityManagerInterface $em): Response
    {
        $idLivret = $request->request->get('idLivret');
        $categorieId = $request->request->get('categorie_id');
        $montant = $request->request->get('montant');
        $description = $request->request->get('description');
        $recurrente = $request->request->get('recurrente');

        if (!$idLivret || !$categorieId || !$montant || !$description) {
            $this->addFlash('danger', 'Tous les champs obligatoires doivent être remplis.');
            return $this->redirectToRoute('detailLivret', ['id' => $idLivret]);
        }

        $livret = $em->getRepository(Livret::class)->find($idLivret);
        $categorie = $em->getRepository(Categorie::class)->find($categorieId);

        if (!$livret || !$categorie) {
            $this->addFlash('danger', 'Livret ou catégorie invalide.');
            return $this->redirectToRoute('detailLivret', ['id' => $idLivret]);
        }

        $depense = new Depense();
        $depense->setLivret($livret);
        $depense->setCategorie($categorie);
        $depense->setMontantDepense((float) $montant);
        $depense->setDescriptionDepense($description);
        $depense->setDateDepense(new \DateTime());

        if ($recurrente) {
            $frequence = $request->request->get('frequence');
            $dateDebut = new \DateTime($request->request->get('date_debut'));
            $dateFin = null;
            if ($request->request->get('date_fin')) {
                $dateFin = new \DateTime($request->request->get('date_fin'));
            }

            $recurrence = new Recurrence();
            $recurrence->setFrequence($frequence);
            $recurrence->setDateDebut($dateDebut);
            $recurrence->setDateFin($dateFin);
            $recurrence->setDepense($depense);

            $depense->setRecurrence($recurrence);
            $em->persist($recurrence);
        }

        $em->persist($depense);
        $em->flush();

        $this->addFlash('success', 'Dépense ajoutée avec succès.');

        return $this->redirectToRoute('detailLivret', ['id' => $idLivret]);
    }


    #[Route('/modifierDepense/{id}', name: 'modifierDepense', methods: ['POST'])]
    public function modifierDepense(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $depense = $em->getRepository(Depense::class)->find($id);

        if (!$depense) {
            $this->addFlash('danger', 'Dépense introuvable.');
            return $this->redirectToRoute('detailLivret', ['id' => $depense->getLivret()->getId()]);
        }

        $montant = $request->request->get('modifMontantDepense');
        $description = $request->request->get('modifDescriptionDepense');
        $recurrente = $request->request->get('modifRecurrenteDepense');

        if (!$montant || !$description) {
            $this->addFlash('danger', 'Tous les champs sont obligatoires.');
            return $this->redirectToRoute('detailLivret', ['id' => $depense->getLivret()->getId()]);
        }

        $depense->setMontantDepense((float) $montant);
        $depense->setDescriptionDepense($description);

        if ($recurrente) {
            $recurrence = $depense->getRecurrence() ?: new Recurrence();

            $frequenceInput = $request->request->get('frequenceModif');
            $dateDebutInput = $request->request->get('date_debut_modif');
            $dateFinInput = $request->request->get('date_fin_modif');

            if ($frequenceInput) {
                $recurrence->setFrequence((int) $frequenceInput);
            }

            if ($dateDebutInput) {
                $recurrence->setDateDebut(new \DateTime($dateDebutInput));
            }

            if ($dateFinInput) {
                $recurrence->setDateFin(new \DateTime($dateFinInput));
            } else {
                $recurrence->setDateFin(null); 
            }

            $recurrence->setDepense($depense);
            $depense->setRecurrence($recurrence);
            $em->persist($recurrence);
        } else {
            $recurrence = $depense->getRecurrence();
            if ($recurrence) {
                $em->remove($recurrence);
            }
        }

        $em->flush();

        $this->addFlash('success', 'Dépense modifiée avec succès.');
        return $this->redirectToRoute('detailLivret', ['id' => $depense->getLivret()->getId()]);
    }


    #[Route('/lesLivrets/{livretId}/supprimerDepense/{depenseId}', name: 'supprimerDepense', methods: ['GET'])]
    public function supprimerDepense(int $livretId, int $depenseId, EntityManagerInterface $em): Response
    {
        $depense = $em->getRepository(Depense::class)->find($depenseId);

        if (!$depense) {
            $this->addFlash('danger', 'Dépense introuvable.');
            return $this->redirectToRoute('detailLivret', ['id' => $livretId]);
        }

        $em->remove($depense);
        $em->flush();

        $this->addFlash('success', 'Dépense supprimée avec succès.');
        return $this->redirectToRoute('detailLivret', ['id' => $livretId]);
    }

}