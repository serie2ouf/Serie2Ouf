<?php

namespace App\Command;

use App\Entity\Categorie;
use App\Entity\News;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:init-data', description: 'Insère les données de départ du TP Electric Mobility')]
final class InitDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // ---------- CATEGORIES ----------
        $categories = [];
        if ($this->em->getRepository(Categorie::class)->count([]) === 0) {
            foreach (['Hoverboards', 'Trottinettes', 'Vélos'] as $libelle) {
                $c = (new Categorie())->setLibelle($libelle);
                $this->em->persist($c);
                $categories[$libelle] = $c;
            }
            $this->em->flush();
            $io->success('3 catégories ajoutées');
        } else {
            foreach ($this->em->getRepository(Categorie::class)->findAll() as $c) {
                $categories[$c->getLibelle()] = $c;
            }
            $io->note('Catégories déjà présentes : rien à faire');
        }

        // ---------- PRODUITS ----------
        if ($this->em->getRepository(Produit::class)->count([]) === 0 && count($categories) === 3) {
            $produits = [
                ['Hoverboards', 'Hoverboard', 'Un hoverboard pour toutes les situations.', '210.00', 'H01.jpg'],
                ['Hoverboards', 'Hoverboard et Hoverkart', 'Passe du mode glisse au mode kart en un instant !', '330.00', 'H02.jpg'],
                ['Hoverboards', 'Hoverboard fun', "L'équilibre parfait entre style, fun et liberté.", '230.00', 'H03.jpg'],
                ['Trottinettes', 'Trottinette électrique Xénon-S', 'Compacte, légère et parfaite pour vos trajets quotidiens.', '625.00', 'T01.jpg'],
                ['Trottinettes', 'Trottinette électrique Xénon-R', 'Puissance, autonomie et design pour une conduite sans limites.', '915.00', 'T02.jpg'],
                ['Trottinettes', 'Trottinette électrique Helium', 'Vitesse, confort et technologie pour les plus exigeants.', '1249.00', 'T03.jpg'],
                ['Vélos', 'Vélo électrique', 'Le compagnon idéal pour une mobilité douce et efficace.', '990.00', 'V01.jpg'],
                ['Vélos', 'Vélo électrique tout terrain', "Conçu pour l'aventure, prêt pour tous les terrains.", '1240.00', 'V02.jpg'],
                ['Vélos', 'Vélo électrique pliable', 'Pratique, rapide et facile à ranger : la liberté urbaine.', '825.00', 'V03.jpg'],
            ];
            foreach ($produits as [$cat, $designation, $detail, $prix, $photo]) {
                $p = (new Produit())
                    ->setUneCategorie($categories[$cat])
                    ->setDesignation($designation)
                    ->setDetail($detail)
                    ->setPrix($prix)
                    ->setPhoto($photo);
                $this->em->persist($p);
            }
            $this->em->flush();
            $io->success('9 produits ajoutés');
        } else {
            $io->note('Produits déjà présents : rien à faire');
        }

        // ---------- NEWS ----------
        if ($this->em->getRepository(News::class)->count([]) === 0) {
            $news = [
                ['Tous en vélo !', '2026-08-18', 'Découvrez nos vélos électriques haute performance', 'Pub_1-1200x467.webp'],
                ['La Trottinette sans effort !', '2026-11-18', 'Mobilité urbaine rapide et écologique', 'Pub_2-1200x467.webp'],
                ['Hoverboard ... toujours plus fun !', '2026-01-09', "L'expérience de mobilité la plus innovante", 'Pub_3-1200x467.webp'],
                ['Tous à scooters !', '2026-01-14', 'Se déplacer plus loin sans bruit !', 'Pub_4-1200x467.webp'],
            ];
            foreach ($news as [$titre, $date, $contenu, $image]) {
                $this->em->persist(
                    (new News())->setTitre($titre)->setDatenews(new \DateTime($date))->setContenu($contenu)->setImage($image)
                );
            }
            $this->em->flush();
            $io->success('4 news ajoutées');
        } else {
            $io->note('News déjà présentes : rien à faire');
        }

        // ---------- UTILISATEURS ----------
        $comptes = [
            ['jean.dumont@gmail.com', 'Admin2026!', 'DUMONT', 'Jean', ['ROLE_ADMIN']],
            ['pierre.dupond@gmail.com', 'Util2026!!', 'DUPOND', 'Pierre', ['ROLE_USER']],
        ];
        foreach ($comptes as [$email, $mdp, $nom, $prenom, $roles]) {
            if ($this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $email])) {
                $io->note("Utilisateur $email déjà présent");
                continue;
            }
            $u = (new Utilisateur())->setEmail($email)->setNom($nom)->setPrenom($prenom)->setRoles($roles);
            $u->setPassword($this->hasher->hashPassword($u, $mdp));
            $this->em->persist($u);
            $io->success("Utilisateur $email créé");
        }
        $this->em->flush();

        $io->writeln('<info>Terminé.</info> Connexion admin : jean.dumont@gmail.com / Admin2026!');

        return Command::SUCCESS;
    }
}
