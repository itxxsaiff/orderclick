<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Store categories get a name per language ({"ar": "...", "de": "...", "fr": "..."}), editable in
 * Admin > Store Categories. Before this, only a dozen categories had a landing.cat_* translation key,
 * so the ~170 categories added from the admin showed in English on the Arabic/German/French pages.
 *
 * Fills names for the categories that exist on the live site (matched by English name) but never
 * overwrites a translation that is already set. Also fixes two typos in category names.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('store_category', 'name_translations')) {
            Schema::table('store_category', function (Blueprint $table) {
                $table->text('name_translations')->nullable()->after('name');
            });
        }

        // Typos in the live data ("Pet Care:" and a stray Arabic word before the English name).
        DB::table('store_category')->where('name', 'Pet Care:')->update(['name' => 'Pet Care']);
        DB::table('store_category')->where('name', 'like', '%. Translation & Localization')
            ->update(['name' => 'Translation & Localization']);

        $map = self::names();
        $lookup = [];
        foreach ($map as $en => $t) {
            $lookup[mb_strtolower(trim($en))] = $t;
        }

        foreach (DB::table('store_category')->select('id', 'name', 'name_translations')->get() as $row) {
            $t = $lookup[mb_strtolower(trim((string) $row->name))] ?? null;
            if (!$t) {
                continue;
            }
            $current = json_decode((string) $row->name_translations, true) ?: [];
            $merged = $current;
            foreach (['ar' => $t[0], 'de' => $t[1], 'fr' => $t[2]] as $code => $value) {
                if (empty($merged[$code])) {
                    $merged[$code] = $value;
                }
            }
            if ($merged !== $current) {
                DB::table('store_category')->where('id', $row->id)
                    ->update(['name_translations' => json_encode($merged, JSON_UNESCAPED_UNICODE)]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('store_category', 'name_translations')) {
            Schema::table('store_category', function (Blueprint $table) {
                $table->dropColumn('name_translations');
            });
        }
    }

    /** English name => [Arabic, German, French] */
    private static function names(): array
    {
        return [
            'Restaurants' => ['مطاعم', 'Restaurants', 'Restaurants'],
            'Cafés' => ['مقاهي', 'Cafés', 'Cafés'],
            'Grocery Stores' => ['محلات البقالة', 'Lebensmittelgeschäfte', 'Épiceries'],
            'Pharmacies' => ['صيدليات', 'Apotheken', 'Pharmacies'],
            'Flower Shops' => ['محلات ورود', 'Blumenläden', 'Fleuristes'],
            'Clothing Stores' => ['متاجر ملابس', 'Bekleidungsgeschäfte', 'Magasins de vêtements'],
            'Gift Shops' => ['محلات هدايا', 'Geschenkläden', 'Boutiques de cadeaux'],
            'Bakeries' => ['مخابز', 'Bäckereien', 'Boulangeries'],
            'Fruit & Vegetable Markets' => ['أسواق الفواكه والخضار', 'Obst- & Gemüsemärkte', 'Marchés de fruits et légumes'],
            'Dairy Stores' => ['محلات ألبان', 'Milchläden', 'Crémeries'],
            'Ice Cream Shops' => ['محلات آيس كريم', 'Eisdielen', 'Glaciers'],
            'Books' => ['الكتب', 'Bücher', 'Livres'],
            'Electronics & Mobiles' => ['الإلكترونيات والجوالات', 'Elektronik & Handys', 'Électronique et mobiles'],
            'Shoes & Footwear' => ['الأحذية', 'Schuhe', 'Chaussures'],
            'Jewelry & Watches' => ['المجوهرات والساعات', 'Schmuck & Uhren', 'Bijoux et montres'],
            'Cosmetics & Perfumes' => ['مستحضرات التجميل والعطور', 'Kosmetik & Parfüms', 'Cosmétiques et parfums'],
            'Toys & Games' => ['الألعاب', 'Spielzeug & Spiele', 'Jouets et jeux'],
            'Sports Equipment' => ['المعدات الرياضية', 'Sportausrüstung', 'Équipements sportifs'],
            'Furniture & Home Decor' => ['الأثاث وديكور المنزل', 'Möbel & Wohndeko', 'Meubles et décoration'],
            'Butcher & Meat' => ['الملاحم واللحوم', 'Metzgerei & Fleisch', 'Boucherie et viandes'],
            'Fish & Seafood' => ['الأسماك والمأكولات البحرية', 'Fisch & Meeresfrüchte', 'Poissons et fruits de mer'],
            'Optics & Eyewear' => ['البصريات والنظارات', 'Optik & Brillen', 'Optique et lunettes'],
            'Roastery & Nuts' => ['المحامص والمكسرات', 'Röstereien & Nüsse', 'Torréfaction et fruits secs'],
            'Auto Parts & Accessories' => ['قطع غيار وإكسسوارات السيارات', 'Autoteile & Zubehör', 'Pièces et accessoires auto'],
            'Sweets & Chocolates' => ['الحلويات والشوكولاتة', 'Süßwaren & Schokolade', 'Confiseries et chocolats'],
            'Home Appliances' => ['الأجهزة المنزلية', 'Haushaltsgeräte', 'Électroménager'],
            'Gyms & Fitness' => ['الصالات الرياضية واللياقة', 'Fitnessstudios', 'Salles de sport et fitness'],
            'Clinics & Medical Centers' => ['العيادات والمراكز الطبية', 'Kliniken & medizinische Zentren', 'Cliniques et centres médicaux'],
            'Salons & Beauty' => ['الصالونات والتجميل', 'Salons & Beauty', 'Salons et beauté'],
            'Spa & Wellness' => ['السبا والعناية', 'Spa & Wellness', 'Spa et bien-être'],
            'Lawyers & Consultants' => ['المحامون والمستشارون', 'Anwälte & Berater', 'Avocats et consultants'],
            'Tutors & Courses' => ['المدرسون والدورات', 'Nachhilfe & Kurse', 'Tuteurs et cours'],
            'Hotels & Accommodation' => ['الفنادق والإقامة', 'Hotels & Unterkünfte', 'Hôtels et hébergement'],
            'Car Rentals' => ['تأجير السيارات', 'Autovermietung', 'Location de voitures'],
            'Event & Venues' => ['قاعات ومواقع الفعاليات', 'Events & Veranstaltungsorte', 'Événements et lieux'],
            'Photographers' => ['المصورون', 'Fotografen', 'Photographes'],
            'Maintenance & Repair' => ['الصيانة والإصلاح', 'Wartung & Reparatur', 'Maintenance et réparation'],
            'Pet Care' => ['رعاية الحيوانات الأليفة', 'Tierpflege', 'Soins pour animaux'],
            'Flights & Travel' => ['الطيران والسفر', 'Flüge & Reisen', 'Vols et voyages'],
            'Dental Clinics' => ['عيادات الأسنان', 'Zahnkliniken', 'Cliniques dentaires'],
            'Hospitals & Specialized Centers' => ['المستشفيات والمراكز التخصصية', 'Krankenhäuser & Fachzentren', 'Hôpitaux et centres spécialisés'],
            'Medical Laboratories' => ['المختبرات الطبية', 'Medizinische Labore', 'Laboratoires médicaux'],
            'Radiology & Imaging Centers' => ['مراكز الأشعة والتصوير', 'Radiologie & Bildgebung', 'Centres de radiologie et d’imagerie'],
            'Physical Therapy & Rehabilitation' => ['العلاج الطبيعي والتأهيل', 'Physiotherapie & Rehabilitation', 'Kinésithérapie et rééducation'],
            'Mental Health & Therapy' => ['الصحة النفسية والعلاج', 'Psychische Gesundheit & Therapie', 'Santé mentale et thérapie'],
            'Home Healthcare' => ['الرعاية الصحية المنزلية', 'Häusliche Pflege', 'Soins à domicile'],
            'Personal Trainers' => ['المدربون الشخصيون', 'Personal Trainer', 'Coachs sportifs'],
            'Sports Courts & Arenas' => ['الملاعب والصالات الرياضية', 'Sportplätze & Arenen', 'Terrains et arènes de sport'],
            'Swimming & Martial Arts' => ['السباحة والفنون القتالية', 'Schwimmen & Kampfsport', 'Natation et arts martiaux'],
            'Ladies Beauty Salons' => ['صالونات التجميل النسائية', 'Damen-Schönheitssalons', 'Salons de beauté pour femmes'],
            'Barbershops' => ['صالونات الحلاقة', 'Barbershops', 'Barbiers'],
            'Dermatology & Cosmetic Clinics' => ['عيادات الجلدية والتجميل', 'Dermatologie & Ästhetik', 'Dermatologie et esthétique'],
            'Business & Financial Advisors' => ['مستشارو الأعمال والمالية', 'Unternehmens- & Finanzberater', 'Conseillers d’affaires et financiers'],
            'Tutors & Academic Lessons' => ['المدرسون والدروس الأكاديمية', 'Nachhilfe & Unterricht', 'Tuteurs et cours académiques'],
            'Meeting Rooms & Co-working Spaces' => ['قاعات الاجتماعات ومساحات العمل المشتركة', 'Meetingräume & Coworking', 'Salles de réunion et coworking'],
            'Chalets & Farms' => ['الشاليهات والمزارع', 'Chalets & Bauernhöfe', 'Chalets et fermes'],
            'Auto Service & Detailing' => ['خدمة وتلميع السيارات', 'Autoservice & Aufbereitung', 'Entretien et esthétique auto'],
            'Real Estate Agencies' => ['الوكالات العقارية', 'Immobilienagenturen', 'Agences immobilières'],
            'Graphic Design' => ['التصميم الجرافيكي', 'Grafikdesign', 'Design graphique'],
            'Website Development' => ['تطوير المواقع', 'Webentwicklung', 'Développement web'],
            'Mobile App Development' => ['تطوير تطبيقات الجوال', 'App-Entwicklung', 'Développement d’applications mobiles'],
            'Video Editing & Montage' => ['تحرير ومونتاج الفيديو', 'Videobearbeitung & Schnitt', 'Montage vidéo'],
            'Digital Marketing & SEO' => ['التسويق الرقمي وتحسين محركات البحث', 'Digitales Marketing & SEO', 'Marketing digital et SEO'],
            'Content Writing & Translation' => ['كتابة المحتوى والترجمة', 'Texterstellung & Übersetzung', 'Rédaction et traduction'],
            'Social Media Management' => ['إدارة وسائل التواصل الاجتماعي', 'Social-Media-Management', 'Gestion des réseaux sociaux'],
            'Home Cleaning & Maid Services' => ['تنظيف المنازل وخدمات العاملات', 'Haushaltsreinigung & Haushaltshilfe', 'Ménage et aide à domicile'],
            'Plumbing & Electrical Work' => ['السباكة والكهرباء', 'Sanitär- & Elektroarbeiten', 'Plomberie et électricité'],
            'Blacksmithing & Metal Works' => ['الحدادة والأعمال المعدنية', 'Schmiede- & Metallarbeiten', 'Ferronnerie et métallerie'],
            'Carpentry & Woodworking' => ['النجارة والأعمال الخشبية', 'Schreinerei & Holzarbeiten', 'Menuiserie et travail du bois'],
            'Aluminum & Glass Works' => ['أعمال الألمنيوم والزجاج', 'Aluminium- & Glasarbeiten', 'Aluminium et vitrerie'],
            'Plumbing & Sanitary Works' => ['أعمال السباكة والأدوات الصحية', 'Sanitärarbeiten', 'Plomberie et sanitaires'],
            'Electrical & Lighting Services' => ['خدمات الكهرباء والإنارة', 'Elektro- & Beleuchtungsservice', 'Électricité et éclairage'],
            'HVAC & AC Maintenance' => ['صيانة التكييف والتهوية', 'Klima- & Lüftungswartung', 'Maintenance de climatisation'],
            'Painting & Wall Finishes' => ['الدهانات وتشطيبات الجدران', 'Malerarbeiten & Wandgestaltung', 'Peinture et finitions murales'],
            'Tiles & Flooring Works' => ['أعمال البلاط والأرضيات', 'Fliesen- & Bodenarbeiten', 'Carrelage et revêtements de sol'],
            'Appliance Repair Services' => ['إصلاح الأجهزة المنزلية', 'Gerätereparatur', 'Réparation d’électroménager'],
            'Locksmith & Key Cutting' => ['الأقفال ونسخ المفاتيح', 'Schlüsseldienst', 'Serrurerie et reproduction de clés'],
            'Towing & Roadside Assistance' => ['السطحات والمساعدة على الطريق', 'Abschleppdienst & Pannenhilfe', 'Remorquage et assistance routière'],
            'Furniture Moving & Relocation' => ['نقل الأثاث', 'Möbeltransport & Umzug', 'Déménagement de meubles'],
            'Private Driver & Chauffeur' => ['سائق خاص', 'Privatfahrer & Chauffeur', 'Chauffeur privé'],
            'Heavy Equipment & Machinery' => ['المعدات والآليات الثقيلة', 'Schwermaschinen', 'Engins et machines lourdes'],
            'Courier & Express Delivery' => ['التوصيل السريع', 'Kurier- & Expresslieferung', 'Coursier et livraison express'],
            'Water Tanker Supply' => ['توريد صهاريج المياه', 'Wassertankwagen', 'Livraison d’eau par citerne'],
            'Pest Control & Fumigation' => ['مكافحة الحشرات والتعقيم', 'Schädlingsbekämpfung', 'Lutte antiparasitaire et désinsectisation'],
            'Deep Cleaning & Housekeeping' => ['التنظيف العميق والتدبير المنزلي', 'Grundreinigung & Hauswirtschaft', 'Nettoyage en profondeur et entretien'],
            'Gas Cylinder Delivery' => ['توصيل اسطوانات الغاز', 'Gasflaschenlieferung', 'Livraison de bouteilles de gaz'],
            'Landscaping & Gardening' => ['تنسيق الحدائق والزراعة', 'Garten- & Landschaftsbau', 'Aménagement paysager et jardinage'],
            'Printing & Signage' => ['الطباعة واللوحات الإعلانية', 'Druck & Beschilderung', 'Impression et signalétique'],
            'Security Systems & CCTV' => ['أنظمة الأمن وكاميرات المراقبة', 'Sicherheitssysteme & Videoüberwachung', 'Systèmes de sécurité et vidéosurveillance'],
            'Private Tutoring' => ['الدروس الخصوصية', 'Privatunterricht', 'Cours particuliers'],
            'Driving Lessons' => ['دروس القيادة', 'Fahrstunden', 'Leçons de conduite'],
            'Celebrities & Public Figures' => ['المشاهير والشخصيات العامة', 'Prominente & Persönlichkeiten', 'Célébrités et personnalités'],
            'Influencer Marketing' => ['التسويق عبر المؤثرين', 'Influencer-Marketing', 'Marketing d’influence'],
            'UGC Content Creators' => ['صناع المحتوى (UGC)', 'UGC-Content-Creator', 'Créateurs de contenu UGC'],
            'Voice Over & Audio Production' => ['التعليق الصوتي والإنتاج الصوتي', 'Voice-over & Audioproduktion', 'Voix off et production audio'],
            'Music & Jingle Production' => ['إنتاج الموسيقى والجينجل', 'Musik- & Jingle-Produktion', 'Production musicale et jingles'],
            '2D & 3D Animation' => ['الرسوم المتحركة ثنائية وثلاثية الأبعاد', '2D- & 3D-Animation', 'Animation 2D et 3D'],
            'Podcast Production & Hosting' => ['إنتاج وتقديم البودكاست', 'Podcast-Produktion & Moderation', 'Production et animation de podcasts'],
            'Event Planning & Management' => ['تخطيط وإدارة الفعاليات', 'Eventplanung & -management', 'Organisation et gestion d’événements'],
            'Party & Wedding Planners' => ['منظمو الحفلات والأعراس', 'Party- & Hochzeitsplaner', 'Organisateurs de fêtes et mariages'],
            'DJ & Sound Engineering' => ['دي جي وهندسة الصوت', 'DJ & Tontechnik', 'DJ et ingénierie du son'],
            'Hospitality & Catering Services & Shive' => ['خدمات الضيافة والتموين', 'Gastgewerbe & Catering', 'Hospitalité et traiteur'],
            'Public Relations & Media (PR)' => ['العلاقات العامة والإعلام', 'Öffentlichkeitsarbeit & Medien (PR)', 'Relations publiques et médias'],
            'Accounting & Tax Auditing' => ['المحاسبة والتدقيق الضريبي', 'Buchhaltung & Steuerprüfung', 'Comptabilité et audit fiscal'],
            'Translation & Localization' => ['الترجمة والتوطين', 'Übersetzung & Lokalisierung', 'Traduction et localisation'],
            'Email & WhatsApp Marketing' => ['التسويق عبر البريد وواتساب', 'E-Mail- & WhatsApp-Marketing', 'Marketing par e-mail et WhatsApp'],
            'Cybersecurity & IT Solutions' => ['الأمن السيبراني وحلول تقنية المعلومات', 'Cybersicherheit & IT-Lösungen', 'Cybersécurité et solutions informatiques'],
            'Virtual Assistant & Data Entry' => ['المساعد الافتراضي وإدخال البيانات', 'Virtuelle Assistenz & Dateneingabe', 'Assistance virtuelle et saisie de données'],
            'Currency Exchange & Remittance' => ['الصرافة والحوالات', 'Geldwechsel & Überweisungen', 'Change et transferts d’argent'],
            'Cargo & Shipping Services' => ['خدمات الشحن', 'Fracht- & Versanddienste', 'Fret et expédition'],
            'Disposable & Plastic Products' => ['المنتجات البلاستيكية وأحادية الاستخدام', 'Einweg- & Kunststoffprodukte', 'Produits jetables et plastiques'],
            'Logistics & Supply Chain' => ['الخدمات اللوجستية وسلاسل الإمداد', 'Logistik & Lieferkette', 'Logistique et chaîne d’approvisionnement'],
            'Veterinary Clinics & Doctors' => ['العيادات والأطباء البيطريون', 'Tierkliniken & Tierärzte', 'Cliniques et médecins vétérinaires'],
            'Kitchenware & Cookware' => ['أدوات المطبخ والطبخ', 'Küchen- & Kochgeschirr', 'Ustensiles de cuisine'],
            'Packaging & Disposables' => ['التغليف والمستلزمات أحادية الاستخدام', 'Verpackung & Einwegartikel', 'Emballages et articles jetables'],
            'Sanitary & Cleaning Supplies' => ['المستلزمات الصحية ومواد التنظيف', 'Sanitär- & Reinigungsbedarf', 'Produits sanitaires et d’entretien'],
            'Fabrics & Textiles' => ['الأقمشة والمنسوجات', 'Stoffe & Textilien', 'Tissus et textiles'],
            'Camping & Outdoor Gear' => ['مستلزمات التخييم والرحلات', 'Camping- & Outdoor-Ausrüstung', 'Équipement de camping et plein air'],
            'Hunting & Marine Supplies' => ['مستلزمات الصيد والبحر', 'Jagd- & Marinebedarf', 'Articles de chasse et nautisme'],
            'Customs Clearance Agents' => ['مخلصو الجمارك', 'Zollabfertigung', 'Agents en dédouanement'],
            'Home Healthcare & Nursing' => ['الرعاية الصحية والتمريض المنزلي', 'Häusliche Pflege & Krankenpflege', 'Soins et infirmiers à domicile'],
            'Optometrists & Eye Exams' => ['أخصائيو البصريات وفحص النظر', 'Optometristen & Sehtests', 'Optométristes et examens de la vue'],
            'Equestrian & Horse Riding' => ['الفروسية وركوب الخيل', 'Reitsport & Reiten', 'Équitation'],
            'Yacht & Boat Rentals' => ['تأجير اليخوت والقوارب', 'Yacht- & Bootsverleih', 'Location de yachts et bateaux'],
            'Packing & Unpacking Services' => ['خدمات التغليف والفك', 'Ein- & Auspackservice', 'Services d’emballage et déballage'],
            'Warehouse Storage Services' => ['خدمات التخزين والمستودعات', 'Lagerdienstleistungen', 'Services d’entreposage'],
            'Forklift & Crane Services' => ['خدمات الرافعات الشوكية والكرينات', 'Gabelstapler- & Krandienste', 'Services de chariots élévateurs et grues'],
            'Car Dealerships & Showrooms' => ['وكالات ومعارض السيارات', 'Autohäuser & Ausstellungsräume', 'Concessionnaires et showrooms auto'],
            'Used Cars & Brokers' => ['السيارات المستعملة والوسطاء', 'Gebrauchtwagen & Händler', 'Voitures d’occasion et courtiers'],
            'Motorcycles & Powersports' => ['الدراجات النارية والرياضات الآلية', 'Motorräder & Powersports', 'Motos et sports motorisés'],
            'Car Wash & Detailing' => ['غسيل وتلميع السيارات', 'Autowäsche & Aufbereitung', 'Lavage et esthétique auto'],
            'Mobile Car Wash' => ['غسيل السيارات المتنقل', 'Mobile Autowäsche', 'Lavage auto à domicile'],
            'Car Tinting & Nano Ceramic' => ['تظليل السيارات والنانو سيراميك', 'Scheibentönung & Nano-Keramik', 'Teinte de vitres et céramique nano'],
            'Car Inspection & Diagnostics' => ['فحص وتشخيص السيارات', 'Fahrzeugprüfung & Diagnose', 'Contrôle et diagnostic auto'],
            'Oil Change & Quick Service & Tires & Wheel Alignment' => ['تغيير الزيت والخدمة السريعة والإطارات وضبط الزوايا', 'Ölwechsel, Schnellservice, Reifen & Achsvermessung', 'Vidange, entretien rapide, pneus et parallélisme'],
            'Tailoring & Alterations' => ['الخياطة والتعديلات', 'Schneiderei & Änderungen', 'Couture et retouches'],
            'Building Materials & Hardware' => ['مواد البناء والعدد', 'Baustoffe & Eisenwaren', 'Matériaux de construction et quincaillerie'],
            'Wholesale & Warehouses' => ['الجملة والمستودعات', 'Großhandel & Lager', 'Vente en gros et entrepôts'],
            'Lighting & Electrical Fixtures' => ['الإنارة والأدوات الكهربائية', 'Beleuchtung & Elektroinstallationen', 'Éclairage et appareillage électrique'],
            'Curtains & Upholstery' => ['الستائر والتنجيد', 'Vorhänge & Polsterei', 'Rideaux et tapisserie'],
            'Antiques & Handicrafts' => ['التحف والحرف اليدوية', 'Antiquitäten & Kunsthandwerk', 'Antiquités et artisanat'],
            'Water Treatment & Filters' => ['معالجة المياه والفلاتر', 'Wasseraufbereitung & Filter', 'Traitement de l’eau et filtres'],
            'Towing & Recovery Services' => ['خدمات السطحات والسحب', 'Abschlepp- & Bergungsdienste', 'Remorquage et dépannage'],
            'Air Conditioning Installation & Repair' => ['تركيب وإصلاح المكيفات', 'Klimaanlagen-Installation & Reparatur', 'Installation et réparation de climatisation'],
            'Roofing & Insulation Services' => ['خدمات الأسقف والعزل', 'Dach- & Dämmarbeiten', 'Toiture et isolation'],
            'Real Estate Viewing & Brokerage' => ['المعاينة والوساطة العقارية', 'Immobilienbesichtigung & Maklerdienste', 'Visites et courtage immobilier'],
            'Chemical & Industrial Engineering' => ['الهندسة الكيميائية والصناعية', 'Chemie- & Wirtschaftsingenieurwesen', 'Génie chimique et industriel'],
            'Electrical Engineering & Power' => ['الهندسة الكهربائية والطاقة', 'Elektrotechnik & Energie', 'Génie électrique et énergie'],
            'Civil & Structural Engineering' => ['الهندسة المدنية والإنشائية', 'Bau- & Tragwerksingenieurwesen', 'Génie civil et structures'],
            'Mechanical & HVAC Engineering' => ['الهندسة الميكانيكية والتكييف', 'Maschinenbau & Klimatechnik', 'Génie mécanique et CVC'],
            'Architectural & Interior Design' => ['التصميم المعماري والداخلي', 'Architektur & Innenarchitektur', 'Architecture et design d’intérieur'],
            'Agricultural & Environmental Engineering' => ['الهندسة الزراعية والبيئية', 'Agrar- & Umwelttechnik', 'Génie agricole et environnemental'],
            'Surveying & Geotechnical Services' => ['المساحة والخدمات الجيوتقنية', 'Vermessung & Geotechnik', 'Topographie et géotechnique'],
            'Engineering Consultation & Site Visits' => ['الاستشارات الهندسية والزيارات الميدانية', 'Ingenieurberatung & Baustellenbesuche', 'Conseil en ingénierie et visites de chantier'],
            'Safety & Occupational Health (OSHA)' => ['السلامة والصحة المهنية', 'Arbeitssicherheit & Gesundheitsschutz', 'Sécurité et santé au travail'],
            'Feasibility Studies & Project Management' => ['دراسات الجدوى وإدارة المشاريع', 'Machbarkeitsstudien & Projektmanagement', 'Études de faisabilité et gestion de projet'],
            'Home Cooked Food & Meals' => ['الأكل البيتي والوجبات', 'Hausgemachtes Essen', 'Plats faits maison'],
            'Custom Cakes & Desserts' => ['الكيك والحلويات حسب الطلب', 'Torten & Desserts nach Maß', 'Gâteaux et desserts sur mesure'],
            'Handmade Crafts & Gifts' => ['الحرف اليدوية والهدايا', 'Handgemachtes & Geschenke', 'Artisanat et cadeaux faits main'],
            'Traditional & Heritage Products' => ['المنتجات التراثية والشعبية', 'Traditionelle Produkte', 'Produits traditionnels et du patrimoine'],
            'Private Chef & Catering' => ['الطاهي الخاص والتموين', 'Privatkoch & Catering', 'Chef privé et traiteur'],
            'Custom Tailoring & Embroidery' => ['التفصيل والتطريز', 'Maßschneiderei & Stickerei', 'Couture sur mesure et broderie'],
            'Cooking & Baking Workshops' => ['ورش الطبخ والخبز', 'Koch- & Backkurse', 'Ateliers de cuisine et pâtisserie'],
            'Dresses & Evening Gown Rentals' => ['تأجير الفساتين وفساتين السهرة', 'Kleider- & Abendkleidverleih', 'Location de robes de soirée'],
            'Party Supplies & Event Rentals' => ['مستلزمات الحفلات وتأجير معدات المناسبات', 'Partybedarf & Eventverleih', 'Articles de fête et location événementielle'],
            'Camera & Gear Rentals' => ['تأجير الكاميرات والمعدات', 'Kamera- & Equipmentverleih', 'Location de caméras et matériel'],
            'Heavy & Construction Equipment Rentals' => ['تأجير المعدات الثقيلة ومعدات البناء', 'Baumaschinenverleih', 'Location d’engins de chantier'],
            'Camping & Desert Gear Rentals' => ['تأجير معدات التخييم والبر', 'Camping- & Wüstenausrüstungsverleih', 'Location de matériel de camping et désert'],
            'Gaming & VR Rentals' => ['تأجير الألعاب والواقع الافتراضي', 'Gaming- & VR-Verleih', 'Location de jeux vidéo et VR'],
            'Medical Equipment Rentals' => ['تأجير المعدات الطبية', 'Medizingeräteverleih', 'Location de matériel médical'],
            'Bicycle & Scooter Rentals' => ['تأجير الدراجات والسكوترات', 'Fahrrad- & Rollerverleih', 'Location de vélos et trottinettes'],
            'Desert Safari & Camps' => ['رحلات السفاري والمخيمات', 'Wüstensafari & Camps', 'Safaris et camps dans le désert'],
            'Diving & Marine Activities' => ['الغوص والأنشطة البحرية', 'Tauchen & Wassersport', 'Plongée et activités nautiques'],
            'Travel & Tourism Agencies' => ['وكالات السفر والسياحة', 'Reisebüros', 'Agences de voyage et de tourisme'],
            'Visa & Passport Assistance' => ['خدمات التأشيرات والجوازات', 'Visa- & Passservice', 'Assistance visa et passeport'],
            'General Contracting & Renovations' => ['المقاولات العامة والترميم', 'Generalunternehmer & Renovierung', 'Entreprise générale et rénovation'],
            'Swimming Pool Maintenance' => ['صيانة المسابح', 'Poolwartung', 'Entretien de piscines'],
            'Recruitment & Human Resources (HR)' => ['التوظيف والموارد البشرية', 'Personalvermittlung & HR', 'Recrutement et ressources humaines'],
        ];
    }
};
