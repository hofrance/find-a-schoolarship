<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur de test
        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);

        // Créer quelques articles de test
        \App\Models\Article::firstOrCreate([
            'slug' => 'comment-choisir-sa-formation-apres-le-bac',
        ], [
            'title' => 'Comment choisir sa formation après le bac',
            'excerpt' => 'Guide complet pour aider les lycéens à choisir leur formation post-bac en fonction de leurs aspirations et compétences.',
            'content' => '<p>Choisir sa formation après le bac est une étape cruciale qui détermine votre avenir professionnel. Voici nos conseils pour faire le bon choix.</p>',
            'category' => 'orientation',
            'tags' => ['orientation', 'post-bac', 'choix'],
            'is_published' => true,
            'published_at' => now(),
            'locale' => 'fr'
        ]);

        \App\Models\Article::firstOrCreate([
            'slug' => 'les-metiers-d-avenir-en-2025',
        ], [
            'title' => 'Les métiers d\'avenir en 2025',
            'excerpt' => 'Découvrez les secteurs porteurs et les métiers qui recruteront dans les prochaines années.',
            'content' => '<p>Le marché du travail évolue rapidement avec l\'émergence de nouveaux métiers. Découvrez les opportunités de demain.</p>',
            'category' => 'conseils',
            'tags' => ['métiers', 'avenir', 'emploi'],
            'is_published' => true,
            'published_at' => now()->subDays(2),
            'locale' => 'fr'
        ]);

        // Créer quelques métiers de test
        \App\Models\Career::firstOrCreate([
            'slug' => 'developpeur-web',
        ], [
            'title' => 'Développeur Web',
            'description' => 'Le développeur web conçoit et réalise des sites internet et des applications web.',
            'requirements' => 'Formation en informatique, maîtrise des langages de programmation web.',
            'skills' => 'HTML, CSS, JavaScript, PHP, bases de données.',
            'salary_range' => '30 000 - 60 000 €/an',
            'education_levels' => ['Bac+2', 'Bac+3', 'Bac+5'],
            'sectors' => ['Informatique', 'Digital', 'Web'],
            'career_prospects' => 'Évolution vers lead developer, chef de projet, freelance.',
            'is_featured' => true,
            'locale' => 'fr'
        ]);

        \App\Models\Career::firstOrCreate([
            'slug' => 'data-scientist',
        ], [
            'title' => 'Data Scientist',
            'description' => 'Le data scientist analyse et exploite les données pour aider à la prise de décision.',
            'requirements' => 'Formation en mathématiques, statistiques ou informatique.',
            'skills' => 'Python, R, SQL, Machine Learning, analyse statistique.',
            'salary_range' => '45 000 - 80 000 €/an',
            'education_levels' => ['Bac+5'],
            'sectors' => ['Informatique', 'Analyse', 'IA'],
            'career_prospects' => 'Lead Data Scientist, Chief Data Officer.',
            'is_featured' => false,
            'locale' => 'fr'
        ]);
    }
}
