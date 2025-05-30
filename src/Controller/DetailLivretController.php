<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Livret;
use App\Entity\Avoir;
use App\Entity\Categorie;
use DateTime;
use DateInterval;

final class DetailLivretController extends AbstractController
{
    #[Route('/lesLivrets/{id}/detail', name: 'detailLivret')]
    public function detail(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $livret = $em->getRepository(Livret::class)->find($id);
        if (!$livret) {
            throw $this->createNotFoundException('Livret non trouvé');
        }

        $mois = $request->query->getInt('mois', (int) date('m'));
        $annee = $request->query->getInt('annee', (int) date('Y'));

        $dateDebut = new DateTime("$annee-$mois-01");
        $dateFin = (clone $dateDebut)->add(new DateInterval('P1M'));

        $moisPrecedent = $mois == 1 ? 12 : $mois - 1;
        $anneePrecedente = $mois == 1 ? $annee - 1 : $annee;

        $moisSuivant = $mois == 12 ? 1 : $mois + 1;
        $anneeSuivante = $mois == 12 ? $annee + 1 : $annee;

        $avoirs = $livret->getAvoirs();
        $avoirsParNom = [];

        // Constitution de la liste des avoirs filtrés sur le libellé
        foreach ($avoirs as $avoir) {
            $categorie = $avoir->getCategorie();
            $nomCategorie = strtolower(trim($categorie->getLibelle()));

            $aDesDepensesDansLeMois = false;
            foreach ($categorie->getDepenses() as $depense) {
                if (
                    $depense->getLivret()->getId() === $livret->getId() &&
                    $depense->getDate() >= $dateDebut &&
                    $depense->getDate() < $dateFin
                ) {
                    $aDesDepensesDansLeMois = true;
                    break;
                }
            }

            if (!isset($avoirsParNom[$nomCategorie])) {
                if ($avoir->estActif() || $aDesDepensesDansLeMois) {
                    $avoirsParNom[$nomCategorie] = $avoir;
                }
            } else {
                $avoirExistant = $avoirsParNom[$nomCategorie];
                if (!$avoir->estActif() && $aDesDepensesDansLeMois && ($avoirExistant->estActif() || !$avoirExistant->estActif())) {
                    $avoirsParNom[$nomCategorie] = $avoir;
                }
            }
        }

        $avoirsFiltres = array_values($avoirsParNom);

        $categoriesToutes = $em->getRepository(Categorie::class)->categOrdreAsc();

        $categoriesDejaUtilisees = [];
        foreach ($avoirsFiltres as $a) {
            $categorie = $a->getCategorie();
            $categoriesDejaUtilisees[] = $categorie;
        }

        $categoriesDisponibles = [];
        foreach ($categoriesToutes as $categorie) {
           

            $estDejaUtilisee = false;

            foreach ($categoriesDejaUtilisees as $categorieUtilisee) {
                if ($categorie['libelle'] == $categorieUtilisee->getLibelle()) {
                    $estDejaUtilisee = true;
                    break;
                }
            }

            if (!$estDejaUtilisee) {
                $categoriesDisponibles[] = $categorie;
            }
        }

        $categoriesDansLivret = [];
        foreach ($avoirsFiltres as $a) {
            $cat = $a->getCategorie();
            if (!in_array($cat, $categoriesDansLivret, true)) {
                $categoriesDansLivret[] = $cat;
            }
        }

        $aujourdhui = new DateTime();
        $moisActuel = (int) $aujourdhui->format('m');
        $anneeActuelle = (int) $aujourdhui->format('Y');
        $estMoisActuelOuFutur = ($annee > $anneeActuelle) || ($annee == $anneeActuelle && $mois >= $moisActuel);

        return $this->render('detailLivret.html.twig', [
            'livret' => $livret,
            'categories' => $categoriesDisponibles,
            'categoriesDansLivret' => $categoriesDansLivret,
            'estMoisActuelOuFutur' => $estMoisActuelOuFutur,
            'avoirs' => $avoirsFiltres,
            'mois' => $mois,
            'annee' => $annee,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'moisPrecedent' => $moisPrecedent,
            'anneePrecedente' => $anneePrecedente,
            'moisSuivant' => $moisSuivant,
            'anneeSuivante' => $anneeSuivante,
        ]);
    }
}
