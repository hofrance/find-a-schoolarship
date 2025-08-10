<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Comment choisir son orientation universitaire',
                'slug' => 'comment-choisir-orientation-universitaire',
                'excerpt' => 'Guide complet pour vous aider à choisir la bonne orientation universitaire selon vos aptitudes et vos objectifs de carrière.',
                'content' => "L'orientation universitaire est une étape cruciale dans la vie de tout étudiant. Elle détermine en grande partie votre future carrière professionnelle.\n\nVoici les étapes importantes à suivre :\n\n1. **Faire un bilan personnel** : Analysez vos forces, vos faiblesses, vos passions et vos valeurs.\n\n2. **Explorer les filières** : Renseignez-vous sur les différentes formations disponibles et leurs débouchés.\n\n3. **Rencontrer des professionnels** : Parlez avec des personnes qui exercent les métiers qui vous intéressent.\n\n4. **Considérer les aspects pratiques** : Budget, durée des études, lieu de formation.\n\n5. **Prendre une décision éclairée** : Pesez le pour et le contre de chaque option.",
                'category' => 'orientation',
                'tags' => ['université', 'choix', 'carrière', 'études'],
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
                'locale' => 'fr',
                'views_count' => 150
            ],
            [
                'title' => 'Les bourses d\'études à l\'étranger : mode d\'emploi',
                'slug' => 'bourses-etudes-etranger-mode-emploi',
                'excerpt' => 'Tout ce que vous devez savoir pour obtenir une bourse d\'études à l\'étranger et réaliser votre rêve d\'étudier dans une université internationale.',
                'content' => "Étudier à l'étranger est une expérience enrichissante qui ouvre de nombreuses portes. Voici comment procéder :\n\n**Types de bourses disponibles :**\n- Bourses gouvernementales\n- Bourses d'excellence\n- Bourses d'organisations internationales\n- Bourses universitaires\n\n**Critères d'éligibilité :**\n- Résultats académiques excellents\n- Niveau de langue requis\n- Projet d'études cohérent\n- Situation financière\n\n**Démarches à suivre :**\n1. Rechercher les bourses disponibles\n2. Préparer son dossier\n3. Rédiger une lettre de motivation\n4. Passer les entretiens\n5. Finaliser l'inscription",
                'category' => 'conseils',
                'tags' => ['bourse', 'étranger', 'international', 'financement'],
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
                'locale' => 'fr',
                'views_count' => 89
            ],
            [
                'title' => 'Préparer son dossier de candidature universitaire',
                'slug' => 'preparer-dossier-candidature-universitaire',
                'excerpt' => 'Les éléments essentiels pour constituer un dossier de candidature universitaire solide et augmenter vos chances d\'admission.',
                'content' => "Un dossier de candidature bien préparé est votre passeport vers l'université de vos rêves.\n\n**Documents indispensables :**\n- Relevés de notes\n- Diplômes et certificats\n- Lettre de motivation personnalisée\n- Lettres de recommandation\n- CV étudiant\n- Portfolio (si requis)\n\n**Conseils pour la lettre de motivation :**\n- Personnalisez pour chaque établissement\n- Montrez votre motivation authentique\n- Expliquez votre projet professionnel\n- Mettez en avant vos expériences\n- Soignez la forme et l'orthographe\n\n**Timeline de préparation :**\n- 6 mois avant : Recherche des formations\n- 4 mois avant : Préparation des documents\n- 2 mois avant : Finalisation et envoi\n- 1 mois avant : Suivi des candidatures",
                'category' => 'etudes',
                'tags' => ['candidature', 'dossier', 'université', 'admission'],
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(3),
                'locale' => 'fr',
                'views_count' => 234
            ],
            [
                'title' => 'Les métiers du numérique : opportunités et formations',
                'slug' => 'metiers-numerique-opportunites-formations',
                'excerpt' => 'Découvrez les métiers d\'avenir dans le secteur du numérique et les formations pour y accéder.',
                'content' => "Le secteur du numérique offre de nombreuses opportunités d'emploi avec des perspectives d'évolution intéressantes.\n\n**Métiers en forte demande :**\n- Développeur web/mobile\n- Data scientist\n- Cybersécurité\n- UX/UI Designer\n- Chef de projet digital\n- Intelligence artificielle\n\n**Formations possibles :**\n- École d'ingénieur\n- Master informatique\n- BTS/DUT informatique\n- Formations courtes spécialisées\n- Autoformation\n\n**Compétences recherchées :**\n- Maîtrise des langages de programmation\n- Capacité d'adaptation\n- Esprit analytique\n- Travail en équipe\n- Veille technologique\n\n**Salaires moyens :**\n- Junior : 35-45k€\n- Confirmé : 45-65k€\n- Senior : 65k€+",
                'category' => 'orientation',
                'tags' => ['numérique', 'informatique', 'tech', 'métiers'],
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(1),
                'locale' => 'fr',
                'views_count' => 67
            ]
        ];

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }
    }
}
