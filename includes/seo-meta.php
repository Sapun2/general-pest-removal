<?php
/**
 * SEO Meta Helper
 * -------------------------------------------------
 * Usage (in each page file, BEFORE require header.php):
 *
 *   $page_seo = get_page_seo('services', [
 *       'title'       => 'Pest Control Services | General Pest Removal',
 *       'description' => 'Science-based, eco-friendly pest control...',
 *       'canonical'   => 'https://generalpestremoval.com/services',
 *       'schema'      => [...],   // PHP array → encoded to JSON-LD
 *       'breadcrumbs' => [['name'=>'Home','url'=>'/'], ['name'=>'Services','url'=>'/services']],
 *   ]);
 */

/**
 * Load SEO settings merged from DB override + page defaults.
 * DB values take precedence when non-empty.
 */
function get_page_seo(string $page_key, array $defaults = []): array
{
    global $pdo;

    $db = [];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM seo_settings WHERE page_key = ? LIMIT 1");
            $stmt->execute([$page_key]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $db = $row;
            }
        } catch (PDOException $e) {
            // Table may not exist yet — fail silently and use defaults
        }
    }

    // Map DB column names → seo array keys
    $map = [
        'meta_title'       => 'title',
        'meta_description' => 'description',
        'og_title'         => 'og_title',
        'og_description'   => 'og_description',
        'og_image'         => 'og_image',
        'canonical_url'    => 'canonical',
        'noindex'          => 'noindex',
    ];

    $merged = $defaults;
    foreach ($map as $db_col => $seo_key) {
        if (!empty($db[$db_col])) {
            $merged[$seo_key] = $db[$db_col];
        }
    }

    return $merged;
}

/**
 * Persist SEO settings to DB (used by admin panel).
 */
function save_page_seo(string $page_key, string $page_label, array $data): bool
{
    global $pdo;
    if (!$pdo) {
        return false;
    }

    try {
        $sql = "INSERT INTO seo_settings
                    (page_key, page_label, meta_title, meta_description, og_title, og_description, og_image, canonical_url, noindex)
                VALUES
                    (:key, :label, :title, :desc, :og_title, :og_desc, :og_img, :canonical, :noindex)
                ON DUPLICATE KEY UPDATE
                    page_label       = VALUES(page_label),
                    meta_title       = VALUES(meta_title),
                    meta_description = VALUES(meta_description),
                    og_title         = VALUES(og_title),
                    og_description   = VALUES(og_description),
                    og_image         = VALUES(og_image),
                    canonical_url    = VALUES(canonical_url),
                    noindex          = VALUES(noindex)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':key'       => $page_key,
            ':label'     => $page_label,
            ':title'     => $data['meta_title']       ?? '',
            ':desc'      => $data['meta_description'] ?? '',
            ':og_title'  => $data['og_title']         ?? '',
            ':og_desc'   => $data['og_description']   ?? '',
            ':og_img'    => $data['og_image']         ?? '',
            ':canonical' => $data['canonical_url']    ?? '',
            ':noindex'   => isset($data['noindex']) ? 1 : 0,
        ]);
    } catch (PDOException $e) {
        error_log("Save SEO error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create all required DB tables and seed initial data (run once via admin).
 */
function setup_db_tables(): array
{
    global $pdo;
    if (!$pdo) {
        return ['error' => 'No database connection. Check db.php credentials.'];
    }

    $results = [];

    $tables = [
        "seo_settings" => "CREATE TABLE IF NOT EXISTS seo_settings (
            id               INT AUTO_INCREMENT PRIMARY KEY,
            page_key         VARCHAR(100) NOT NULL UNIQUE,
            page_label       VARCHAR(200) NOT NULL,
            meta_title       VARCHAR(255) NOT NULL DEFAULT '',
            meta_description TEXT        NOT NULL,
            og_title         VARCHAR(255) NOT NULL DEFAULT '',
            og_description   TEXT        NOT NULL,
            og_image         VARCHAR(500) NOT NULL DEFAULT '',
            canonical_url    VARCHAR(500) NOT NULL DEFAULT '',
            noindex          TINYINT(1)  NOT NULL DEFAULT 0,
            updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "blog_posts" => "CREATE TABLE IF NOT EXISTS blog_posts (
            id               INT AUTO_INCREMENT PRIMARY KEY,
            slug             VARCHAR(200) NOT NULL UNIQUE,
            title            VARCHAR(500) NOT NULL,
            excerpt          TEXT        NOT NULL,
            content          LONGTEXT    NOT NULL,
            category         VARCHAR(100) NOT NULL DEFAULT 'General',
            featured_image   VARCHAR(500) NOT NULL DEFAULT '',
            meta_title       VARCHAR(255) NOT NULL DEFAULT '',
            meta_description TEXT        NOT NULL,
            og_image         VARCHAR(500) NOT NULL DEFAULT '',
            author           VARCHAR(200) NOT NULL DEFAULT 'General Pest Removal Team',
            is_published     TINYINT(1)  NOT NULL DEFAULT 1,
            published_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "bookings" => "CREATE TABLE IF NOT EXISTS bookings (
            id            INT AUTO_INCREMENT PRIMARY KEY,
            first_name    VARCHAR(100) NOT NULL,
            last_name     VARCHAR(100) NOT NULL,
            phone         VARCHAR(20)  NOT NULL,
            email         VARCHAR(200) NOT NULL DEFAULT '',
            city          VARCHAR(100) NOT NULL,
            pest_type     VARCHAR(100) NOT NULL,
            property_type VARCHAR(50)  NOT NULL DEFAULT 'Residential',
            message       TEXT        NOT NULL,
            status        VARCHAR(50)  NOT NULL DEFAULT 'new',
            created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "admin_users" => "CREATE TABLE IF NOT EXISTS admin_users (
            id            INT AUTO_INCREMENT PRIMARY KEY,
            username      VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "contacts" => "CREATE TABLE IF NOT EXISTS contacts (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            name       VARCHAR(200) NOT NULL,
            email      VARCHAR(200) NOT NULL,
            phone      VARCHAR(30)  NOT NULL DEFAULT '',
            message    TEXT         NOT NULL,
            status     VARCHAR(20)  NOT NULL DEFAULT 'unread',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "services" => "CREATE TABLE IF NOT EXISTS services (
            id               INT AUTO_INCREMENT PRIMARY KEY,
            slug             VARCHAR(100) NOT NULL UNIQUE,
            name             VARCHAR(200) NOT NULL,
            tagline          VARCHAR(300) DEFAULT NULL,
            description      TEXT,
            icon             VARCHAR(50)  NOT NULL DEFAULT 'fa-shield-bug',
            badge_text       VARCHAR(100) DEFAULT NULL,
            image_path       VARCHAR(500) DEFAULT NULL,
            features         JSON,
            link_anchor      VARCHAR(100) DEFAULT NULL,
            sort_order       INT          NOT NULL DEFAULT 0,
            is_active        TINYINT(1)  NOT NULL DEFAULT 1,
            meta_title       VARCHAR(255) NOT NULL DEFAULT '',
            meta_description VARCHAR(500) NOT NULL DEFAULT '',
            created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "site_config" => "CREATE TABLE IF NOT EXISTS site_config (
            config_key   VARCHAR(100) NOT NULL PRIMARY KEY,
            config_value TEXT,
            updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];

    foreach ($tables as $name => $sql) {
        try {
            $pdo->exec($sql);
            $results[] = "✓ Table `{$name}` ready";
        } catch (PDOException $e) {
            $results[] = "✗ Table `{$name}` error: " . $e->getMessage();
        }
    }

    // Seed default admin if none exists
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($count == 0) {
            $hash = password_hash('admin123', PASSWORD_BCRYPT);
            $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)")
                ->execute(['admin', $hash]);
            $results[] = "✓ Default admin created (username: admin / password: admin123) — change this after first login!";
        } else {
            $results[] = "✓ Admin users already exist";
        }
    } catch (PDOException $e) {
        $results[] = "✗ Admin seed error: " . $e->getMessage();
    }

    // Seed blog posts if none exist
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
        if ($count == 0) {
            $posts = [
                [
                    'slug'             => 'termite-inspection-sydney-brisbane',
                    'title'            => 'Why Every Sydney & Brisbane Home Needs an Annual Termite Inspection',
                    'excerpt'          => "Australia's warm, humid climate makes Sydney and Brisbane two of the country's highest-risk termite zones. Learn what an AS 3660.2-compliant inspection covers and how to protect your home year-round.",
                    'content'          => '<h2>Why Sydney & Brisbane Have Australia\'s Highest Termite Risks</h2><p>Australia\'s subtropical climate — warm temperatures year-round and high humidity — creates ideal conditions for termite activity in both Sydney and Brisbane. Subterranean termites (<em>Coptotermes acinaciformis</em>) are responsible for more structural damage to Australian homes than fires, storms, and floods combined. In Sydney and Brisbane, an undetected colony can cause $50,000–$200,000 in structural damage before homeowners notice any visible signs.</p><h2>What an AS 3660.2-Compliant Inspection Covers</h2><p><strong>Visual Inspection:</strong> Our technicians inspect all accessible areas of the structure including subfloor, roof void, internal rooms, and external perimeter for evidence of termite activity, workings, and conducive conditions.</p><p><strong>Moisture Mapping:</strong> Termites are attracted to moisture. We use calibrated moisture meters to identify high-risk zones such as leaking pipes, poor drainage, and condensation points.</p><p><strong>Thermal Imaging:</strong> Thermal cameras detect heat signatures consistent with termite activity inside walls — without drilling or destructive investigation.</p><p><strong>Written Report:</strong> Every inspection concludes with a detailed written report compliant with AS 3660.2, suitable for property purchase due diligence or insurance purposes.</p><h2>Prevention: Chemical Barriers and Physical Barriers</h2><p>Post-inspection, we can install approved chemical soil barriers (Termidor) or physical barriers (Kordon) around the perimeter and sub-floor of your home. Chemical barriers are effective for 8–10 years when properly maintained. <a href="/services#termites">Learn about our termite treatment options</a> or <a href="/booking?service=termites">book an inspection today</a>.</p>',
                    'category'         => 'Termites',
                    'featured_image'   => '/assets/images/2.png',
                    'meta_title'       => 'Annual Termite Inspections Sydney & Brisbane | AS 3660.2 Compliant | General Pest Removal',
                    'meta_description' => 'Why Sydney and Brisbane homeowners need annual termite inspections. AS 3660.2-compliant thermal and chemical detection. Licensed team. Book online today.',
                ],
                [
                    'slug'             => 'signs-of-rodent-problem',
                    'title'            => '5 Warning Signs You Have a Rodent Problem in Your Home',
                    'excerpt'          => 'As winter arrives, mice and rats seek warmth indoors across Sydney and Brisbane. Catch the infestation early with these five key warning signs before it escalates.',
                    'content'          => '<h2>Why Rodent Activity Peaks in Winter Across Sydney & Brisbane</h2><p>Even in Australia\'s mild winters, rodents seek shelter, food, and warmth inside homes from June through August. A single mouse can squeeze through a gap of just 6mm — making older Federation homes in Sydney and Queensland timber homes in Brisbane particularly vulnerable. Once inside, a pair of mice can produce over 60 offspring per year.</p><h2>5 Warning Signs to Watch For</h2><p><strong>1. Droppings:</strong> Dark, pellet-shaped mouse droppings (3–6mm) or larger rat droppings (12–18mm) near food sources, inside cabinets, or along skirting boards indicate active infestation.</p><p><strong>2. Gnaw Marks:</strong> Fresh gnaw marks on wood, plastic food containers, electrical wiring (a fire hazard), and plumbing pipes indicate ongoing rodent activity. Older marks fade to grey.</p><p><strong>3. Nesting Materials:</strong> Rodents shred paper, insulation, and fabric to build nests. Check roof voids, wall cavities, under kitchen appliances, and in stored boxes.</p><p><strong>4. Scratching Sounds at Night:</strong> Scurrying, scratching, or squeaking inside walls, ceilings, or the subfloor after dark strongly indicates rodent activity. Rats are most active in the first few hours after sunset.</p><p><strong>5. Smear Marks and Tracks:</strong> Rodents have oily fur and follow fixed pathways, leaving dark grease marks along walls and baseboards. Flour or talcum powder sprinkled along suspected runways will reveal footprints overnight.</p><h2>What To Do Next</h2><p>Our <a href="/services#rodents">rodent control team</a> provides a full inspection, identifies all entry points, and implements a permanent exclusion and baiting program. <a href="/booking?service=rodents">Book a rodent inspection today</a>.</p>',
                    'category'         => 'Rodent Control',
                    'featured_image'   => '/assets/images/3.png',
                    'meta_title'       => '5 Signs of a Rodent Problem in Your Home | General Pest Removal',
                    'meta_description' => 'Scratching in the walls? Droppings in the kitchen? Learn the top 5 warning signs of a rodent infestation and when to call our professional rodent exterminators across Sydney & Brisbane.',
                ],
                [
                    'slug'             => 'eradicating-german-cockroaches',
                    'title'            => 'Commercial Kitchens: Eradicating German Cockroaches for Good',
                    'excerpt'          => 'A guide for Sydney and Brisbane restaurant owners on food authority compliance and why standard sprays consistently fail against resilient German cockroach populations.',
                    'content'          => '<h2>Why German Cockroaches Are the #1 Commercial Pest in Sydney & Brisbane</h2><p>The German cockroach (<em>Blattella germanica</em>) is the most commonly encountered roach in Sydney and Brisbane restaurants, food processing facilities, and multi-unit residential buildings. Unlike other species, German roaches prefer the indoors, breed at an astonishing rate (a single female can produce 400 offspring in her lifetime), and have developed resistance to many common over-the-counter pesticides.</p><h2>Why DIY Sprays Fail</h2><p>The biggest mistake restaurant owners make is reaching for a can of aerosol spray. While this kills individual cockroaches on contact, it disperses the colony, pushing roaches deeper into cracks and into new areas of the kitchen. After years of widespread spray use, many cockroach populations have developed pyrethroid resistance.</p><h2>The Professional Approach: Gel Baiting</h2><p>Gel bait is the most effective treatment for German cockroach infestations. Applied in small quantities in harborage areas (cracks behind appliances, inside electrical outlets, beneath refrigerators), gel bait is carried back to the nest by foraging roaches, poisoning the entire colony including egg-carrying females.</p><h2>Food Safety Compliance for Restaurants</h2><p>A cockroach sighting during a food authority inspection can result in an immediate improvement notice or closure order. Food operators must maintain pest-free environments under Australian food safety laws. We provide post-treatment documentation suitable for food authority compliance across NSW and QLD. <a href="/booking?service=cockroaches">Book a commercial kitchen inspection today</a>.</p>',
                    'category'         => 'Commercial',
                    'featured_image'   => '/assets/images/4.png',
                    'meta_title'       => 'Eradicating German Cockroaches in Commercial Kitchens | Sydney & Brisbane',
                    'meta_description' => 'Why DIY sprays fail and how professional gel baiting eliminates German cockroach colonies in Sydney and Brisbane restaurants. Food authority compliance documentation included.',
                ],
                [
                    'slug'             => 'integrated-pest-management',
                    'title'            => 'What is Integrated Pest Management (IPM)?',
                    'excerpt'          => 'Discover why modern pest control companies are shifting away from toxic chemicals toward sustainable, long-term exclusion strategies.',
                    'content'          => '<h2>Beyond "Spray and Pray": The IPM Revolution</h2><p>Integrated Pest Management (IPM) is a science-based, ecosystem-oriented approach to pest control that emphasises long-term prevention through a combination of biological, cultural, physical, and chemical methods. Unlike traditional pest control — which typically involves applying pesticides on a predetermined schedule — IPM uses pest monitoring data to make decisions that minimise risks to human health, beneficial organisms, and the environment.</p><h2>The 4 Core Principles of IPM</h2><p><strong>1. Monitoring & Identification:</strong> Before any treatment begins, we identify the exact pest species, understand its life cycle, and determine the extent of the infestation. Misidentification is the #1 cause of failed treatments.</p><p><strong>2. Prevention:</strong> The most cost-effective pest control is structural. This means sealing foundation cracks, installing door sweeps, repairing damaged window screens, and eliminating moisture sources that attract pests.</p><p><strong>3. Control Thresholds:</strong> Not every pest sighting requires chemical treatment. IPM establishes health-based thresholds before escalating to chemical controls.</p><p><strong>4. Targeted Treatment:</strong> When chemicals are needed, IPM practitioners select the least toxic, most targeted option available. Gel baits instead of broadcast sprays. Pheromone traps instead of broad pesticide applications.</p><h2>Why IPM Is the Future of General Pest Removal</h2><p>Australian regulators increasingly support IPM practices. For families across Sydney, Inner West, North Shore, Brisbane CBD, Southside Brisbane, and beyond who want eco-friendly options, IPM delivers superior results without compromising indoor air quality. <a href="/services">Learn about our IPM-based services</a> or <a href="/booking">book a free inspection</a>.</p>',
                    'category'         => 'Eco-Friendly',
                    'featured_image'   => '',
                    'meta_title'       => 'What is Integrated Pest Management (IPM)? | General Pest Removal',
                    'meta_description' => 'Learn how IPM (Integrated Pest Management) delivers superior pest control results while using fewer chemicals. Our eco-friendly approach across Sydney & Brisbane explained.',
                ],
            ];

            $stmt = $pdo->prepare(
                "INSERT INTO blog_posts (slug, title, excerpt, content, category, featured_image, meta_title, meta_description)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            foreach ($posts as $p) {
                $stmt->execute([
                    $p['slug'], $p['title'], $p['excerpt'], $p['content'],
                    $p['category'], $p['featured_image'], $p['meta_title'], $p['meta_description'],
                ]);
            }
            $results[] = "✓ Seeded " . count($posts) . " blog posts";
        } else {
            $results[] = "✓ Blog posts already exist";
        }
    } catch (PDOException $e) {
        $results[] = "✗ Blog seed error: " . $e->getMessage();
    }

    // Seed services if none exist
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
        if ($count == 0) {
            $services = [
                [
                    'slug'             => 'termites',
                    'name'             => 'Termite Inspection & Treatment',
                    'tagline'          => 'Thermal imaging inspections. Proven barrier treatments.',
                    'description'      => '<p>Sydney and Brisbane have some of Australia\'s highest bed bug infestation rates. We provide discreet, rapid-response eradication for apartments, condos, and homes across Sydney &amp; Brisbane using chemical-free heat treatments.</p>',
                    'icon'             => 'fa-house-crack',
                    'badge_text'       => '#1 Requested Service',
                    'image_path'       => '/assets/images/2.png',
                    'features'         => json_encode([
                        ['icon' => 'fa-fire',      'title' => 'Thermal Heat Treatments',  'desc' => 'Chemical-free, single-visit complete elimination.'],
                        ['icon' => 'fa-spray-can', 'title' => 'Chemical Applications',    'desc' => 'Multi-visit targeted sprays for severe infestations.'],
                        ['icon' => 'fa-bed',       'title' => 'Mattress Encasements',     'desc' => 'Preventive covers to protect your investment.'],
                    ]),
                    'link_anchor'      => '#bedbugs',
                    'sort_order'       => 1,
                    'is_active'        => 1,
                    'meta_title'       => 'Termite Inspection & Treatment Sydney & Brisbane | Heat Treatment Specialists',
                    'meta_description' => 'Professional termite inspections with a single-visit proven result. Discreet, rapid-response service across Sydney, Eastern Suburbs, Brisbane, and surrounds.',
                ],
                [
                    'slug'             => 'rodents',
                    'name'             => 'Spider Control',
                    'tagline'          => 'Mice & rats — we eliminate and prevent re-entry.',
                    'description'      => '<p>Urban construction across Sydney and Brisbane constantly displaces rodents, driving them into residential homes and commercial kitchens. We don\'t just trap — we prevent re-entry with comprehensive exclusion services.</p>',
                    'icon'             => 'fa-cheese',
                    'badge_text'       => 'Year-Round Problem',
                    'image_path'       => '/assets/images/3.png',
                    'features'         => json_encode([
                        ['icon' => 'fa-hammer', 'title' => 'Exclusion Services',  'desc' => 'Sealing entry points and structural gaps with rodent-proof materials.'],
                        ['icon' => 'fa-cheese', 'title' => 'Baiting & Trapping', 'desc' => 'Strategic placement of secure, tamper-resistant monitoring stations.'],
                        ['icon' => 'fa-shield', 'title' => 'Seasonal Contracts', 'desc' => 'Fall & Winter prevention programs to stop infestations before they start.'],
                    ]),
                    'link_anchor'      => '#rodents',
                    'sort_order'       => 2,
                    'is_active'        => 1,
                    'meta_title'       => 'Spider Control | General Pest Removal',
                    'meta_description' => 'Professional mice and rat extermination with full exclusion services. We seal entry points and prevent re-infestation across Sydney and Brisbane.',
                ],
                [
                    'slug'             => 'cockroaches',
                    'name'             => 'Cockroach & Ant Eradication',
                    'tagline'          => 'IPM-based colony elimination at the source.',
                    'description'      => '<p>German and American cockroaches thrive in Sydney and Brisbane causing health risks and structural damage. We eliminate the colony at its source using IPM-based methods including targeted gel baiting.</p>',
                    'icon'             => 'fa-bug',
                    'badge_text'       => 'Residential & Commercial',
                    'image_path'       => '/assets/images/4.png',
                    'features'         => json_encode([
                        ['icon' => 'fa-droplet',     'title' => 'Targeted Gel Baits',    'desc' => 'Highly effective for kitchen and bathroom roach infestations.'],
                        ['icon' => 'fa-house-crack', 'title' => 'Carpenter Ant Control', 'desc' => 'Baiting and dusting to protect wooden structures from damage.'],
                        ['icon' => 'fa-leaf',        'title' => 'Eco-Friendly Options',  'desc' => 'Pet-safe and family-friendly treatments available.'],
                    ]),
                    'link_anchor'      => '#cockroaches',
                    'sort_order'       => 3,
                    'is_active'        => 1,
                    'meta_title'       => 'Cockroach & Ant Extermination Sydney & Brisbane | General Pest Removal',
                    'meta_description' => 'Eliminate German cockroaches and Carpenter Ants with IPM-based gel baiting across Sydney and Brisbane. Health code compliance documentation for restaurants included.',
                ],
            ];

            $stmt = $pdo->prepare(
                "INSERT INTO services (slug, name, tagline, description, icon, badge_text, image_path, features, link_anchor, sort_order, is_active, meta_title, meta_description)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            foreach ($services as $svc) {
                $stmt->execute([
                    $svc['slug'], $svc['name'], $svc['tagline'], $svc['description'],
                    $svc['icon'], $svc['badge_text'], $svc['image_path'], $svc['features'],
                    $svc['link_anchor'], $svc['sort_order'], $svc['is_active'],
                    $svc['meta_title'], $svc['meta_description'],
                ]);
            }
            $results[] = "✓ Seeded " . count($services) . " default services";
        } else {
            $results[] = "✓ Services already exist";
        }
    } catch (PDOException $e) {
        $results[] = "✗ Services seed error: " . $e->getMessage();
    }

    // Seed site_config defaults if table is empty
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM site_config")->fetchColumn();
        if ($count == 0) {
            $defaults = [
                'business_name'       => 'General Pest Removal',
                'tagline'             => "Sydney & Brisbane's Trusted Pest Control Service",
                'logo_type'           => 'text',
                'logo_icon'           => 'fa-bug',
                'logo_text_primary'   => 'General',
                'logo_text_secondary' => 'Pest',
                'logo_image_url'      => '',
                'phone_primary'       => '(02) 8155 0198',
                'phone_primary_raw'   => '+61281550198',
                'phone_secondary'     => '',
                'phone_secondary_raw' => '',
                'email_primary'       => 'info@generalpestremoval.com',
                'email_admin'         => 'info@generalpestremoval.com',
                'address'             => 'Sydney, NSW & Brisbane, QLD, Australia',
                'hours_weekday'       => 'Mon–Sat, 7am–8pm',
                'hours_emergency'     => '24/7 Emergency Line',
                'social_facebook'     => '',
                'social_instagram'    => '',
                'social_tiktok'       => '',
                'social_google'       => '',
                'social_yelp'         => '',
            ];
            $stmt = $pdo->prepare("INSERT INTO site_config (config_key, config_value) VALUES (?, ?)");
            foreach ($defaults as $k => $v) {
                $stmt->execute([$k, $v]);
            }
            $results[] = "✓ Seeded default site configuration";
        } else {
            $results[] = "✓ Site config already exists";
        }
    } catch (PDOException $e) {
        $results[] = "✗ Site config seed error: " . $e->getMessage();
    }

    return $results;
}
