<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Career;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        $careers = [
            [
                'title' => 'Ingénieur Logiciel',
                'slug' => 'ingenieur-logiciel',
                'description' => 'Conception, développement et maintenance d\'applications et systèmes informatiques.',
                'requirements' => "Formation requise :\n- Master en informatique ou école d'ingénieur\n- Connaissance des langages de programmation\n- Maîtrise des méthodologies de développement\n\nExpérience :\n- Stage ou projet personnel recommandé\n- Participation à des projets open source",
                'skills' => "Compétences techniques :\n- Langages : Java, Python, C++, JavaScript\n- Frameworks : Spring, React, Angular\n- Bases de données : SQL, NoSQL\n- DevOps : Git, Docker, CI/CD\n\nSoft skills :\n- Résolution de problèmes\n- Travail en équipe\n- Communication technique\n- Adaptabilité",
                'salary_range' => '40 000€ - 80 000€',
                'education_levels' => ['master', 'école ingénieur'],
                'sectors' => ['informatique', 'tech', 'numérique'],
                'career_prospects' => "Évolutions possibles :\n- Lead Developer\n- Architecte logiciel\n- Chef de projet technique\n- CTO\n- Expert technique\n- Consultant\n\nSecteurs d'emploi :\n- ESN (Entreprises de Services Numériques)\n- Startups\n- Grandes entreprises\n- Secteur public",
                'is_featured' => true,
                'locale' => 'fr',
                'views_count' => 245
            ],
            [
                'title' => 'Data Scientist',
                'slug' => 'data-scientist',
                'description' => 'Analyse de données complexes pour extraire des insights et aider à la prise de décision stratégique.',
                'requirements' => "Formation :\n- Master en mathématiques, statistiques, informatique\n- École d'ingénieur avec spécialisation data\n- Formation en machine learning\n\nPrérequis :\n- Solides bases en mathématiques\n- Programmation Python/R\n- Connaissance des bases de données",
                'skills' => "Compétences clés :\n- Python, R, SQL\n- Machine Learning (scikit-learn, TensorFlow)\n- Visualisation (Tableau, PowerBI, matplotlib)\n- Big Data (Spark, Hadoop)\n- Statistiques avancées\n- Business Intelligence",
                'salary_range' => '45 000€ - 90 000€',
                'education_levels' => ['master', 'doctorat'],
                'sectors' => ['data', 'finance', 'marketing', 'santé'],
                'career_prospects' => "Évolutions :\n- Senior Data Scientist\n- Chief Data Officer\n- Data Engineering Manager\n- Consultant en analytics\n- Chercheur en IA\n\nDomaines d'application :\n- Finance et banque\n- E-commerce\n- Santé\n- Transport\n- Marketing digital",
                'is_featured' => true,
                'locale' => 'fr',
                'views_count' => 189
            ],
            [
                'title' => 'Médecin Généraliste',
                'slug' => 'medecin-generaliste',
                'description' => 'Professionnel de santé assurant le diagnostic, le traitement et le suivi médical des patients.',
                'requirements' => "Formation obligatoire :\n- PACES/PASS/LAS (1ère année)\n- 6 années d'études médicales\n- 3 années de spécialisation en médecine générale\n- Thèse de doctorat\n- Inscription à l'Ordre des médecins\n\nQualités requises :\n- Excellent niveau scientifique\n- Résistance au stress\n- Empathie et écoute",
                'skills' => "Compétences médicales :\n- Diagnostic et traitement\n- Prescription médicamenteuse\n- Prévention et dépistage\n- Urgences médicales\n- Communication patient\n\nCompétences transversales :\n- Gestion administrative\n- Formation continue\n- Travail pluridisciplinaire\n- Éthique médicale",
                'salary_range' => '70 000€ - 120 000€',
                'education_levels' => ['doctorat'],
                'sectors' => ['santé', 'médical'],
                'career_prospects' => "Modes d'exercice :\n- Cabinet libéral\n- Maison de santé\n- Centre de santé\n- Hôpital public\n- Médecine du travail\n\nSpécialisations possibles :\n- Médecine d'urgence\n- Gériatrie\n- Pédiatrie\n- Médecine du sport\n- Enseignement médical",
                'is_featured' => false,
                'locale' => 'fr',
                'views_count' => 156
            ],
            [
                'title' => 'Designer UX/UI',
                'slug' => 'designer-ux-ui',
                'description' => 'Conception d\'interfaces utilisateur intuitives et d\'expériences utilisateur optimales pour les applications et sites web.',
                'requirements' => "Formation :\n- École de design\n- Master en design interactif\n- Formation en ergonomie\n- Autoformation possible\n\nPortfolio obligatoire démontrant :\n- Projets UX/UI variés\n- Processus de conception\n- Résultats obtenus",
                'skills' => "Compétences design :\n- Figma, Adobe Creative Suite\n- Prototypage (InVision, Principle)\n- Design thinking\n- Tests utilisateurs\n- Wireframing et mockups\n\nConnaissances techniques :\n- HTML/CSS de base\n- Responsive design\n- Accessibilité web\n- Méthodes agiles",
                'salary_range' => '38 000€ - 65 000€',
                'education_levels' => ['bachelor', 'master'],
                'sectors' => ['design', 'numérique', 'web'],
                'career_prospects' => "Évolutions :\n- Senior UX Designer\n- Lead Designer\n- Design Manager\n- Product Designer\n- Consultant UX\n- Creative Director\n\nSecteurs :\n- Agences digitales\n- Startups\n- Grandes entreprises\n- Freelance",
                'is_featured' => true,
                'locale' => 'fr',
                'views_count' => 134
            ],
            [
                'title' => 'Avocat',
                'slug' => 'avocat',
                'description' => 'Professionnel du droit représentant et conseillant les clients dans leurs démarches juridiques.',
                'requirements' => "Cursus obligatoire :\n- Master 1 en droit (minimum)\n- Examen d'entrée au CRFPA\n- 18 mois de formation pratique\n- CAPA (Certificat d'Aptitude à la Profession d'Avocat)\n- Serment devant la Cour d'appel\n\nSpécialisations possibles :\n- Droit des affaires\n- Droit pénal\n- Droit de la famille\n- Droit social",
                'skills' => "Compétences juridiques :\n- Maîtrise du droit\n- Recherche jurisprudentielle\n- Rédaction d'actes\n- Plaidoirie\n- Négociation\n\nQualités personnelles :\n- Éloquence\n- Rigueur\n- Confidentialité\n- Résistance au stress\n- Sens de l'analyse",
                'salary_range' => '30 000€ - 150 000€+',
                'education_levels' => ['master'],
                'sectors' => ['droit', 'juridique', 'justice'],
                'career_prospects' => "Modes d'exercice :\n- Cabinet individuel\n- Cabinet d'avocats\n- Entreprise (juriste)\n- Administration publique\n- Organisations internationales\n\nSpécialisations :\n- Arbitrage international\n- Propriété intellectuelle\n- Droit fiscal\n- Compliance",
                'is_featured' => false,
                'locale' => 'fr',
                'views_count' => 98
            ],
            [
                'title' => 'Enseignant du secondaire',
                'slug' => 'enseignant-secondaire',
                'description' => 'Transmission de connaissances et accompagnement pédagogique d\'élèves de collège et lycée.',
                'requirements' => "Formation requise :\n- Master MEEF (Métiers de l'Enseignement)\n- Concours CAPES, CAPEPS ou CAPET\n- Année de stage (M2)\n- Titularisation après validation\n\nSpécialisation par discipline :\n- Mathématiques, Français, Histoire\n- Sciences physiques, SVT\n- Langues vivantes\n- Arts, EPS, Technologie",
                'skills' => "Compétences pédagogiques :\n- Didactique de la discipline\n- Gestion de classe\n- Évaluation des élèves\n- Différenciation pédagogique\n- Usage du numérique\n\nQualités humaines :\n- Patience et bienveillance\n- Autorité naturelle\n- Créativité\n- Adaptation\n- Travail en équipe",
                'salary_range' => '25 000€ - 45 000€',
                'education_levels' => ['master'],
                'sectors' => ['éducation', 'enseignement', 'public'],
                'career_prospects' => "Évolutions :\n- Professeur principal\n- Formateur d'enseignants\n- Chef d'établissement\n- Inspecteur pédagogique\n- Conseiller pédagogique\n\nMobilité :\n- Enseignement supérieur\n- Formation pour adultes\n- Édition scolaire\n- Ministère de l'Éducation",
                'is_featured' => false,
                'locale' => 'fr',
                'views_count' => 76
            ]
        ];

        foreach ($careers as $careerData) {
            Career::create($careerData);
        }
    }
}
