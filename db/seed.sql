-- THE DRIVE CLINIC — Database Seed Data (MySQL 8.0+)
USE `drive_clinic`;

-- 1. SETTINGS
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('business_name', 'THE DRIVE CLINIC'),
('tagline', 'WHERE CARS GET THEIR SHINE BACK'),
('phone', '+91 94191 00000'),
('whatsapp', '919419100000'),
('email', 'info@thedriveclinic.com'),
('address', 'Nanak Nagar, Jammu, Jammu & Kashmir 180004, India'),
('map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13426.123456789!2d74.871234!3d32.701234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzLCsDQyJzA0LjQiTiA3NMKwNTInMTYuNCJF!5e0!3m2!1sen!2sin!4v1600000000000!5m2!1sen!2sin'),
('hours_json', '{"weekdays":"09:00 AM - 08:00 PM","sunday":"09:00 AM - 07:00 PM","note":"Open 7 days a week"}'),
('instagram_url', 'https://instagram.com/thedriveclinic'),
('meta_title', 'The Drive Clinic | Premium Car & Bike Detailing, PPF & Wash in Jammu'),
('meta_description', 'The Drive Clinic is Jammu''s premier studio for Paint Protection Film (PPF), ceramic coating, precision bike detailing, steam interior care, and signature wash services in Nanak Nagar.')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- 2. HERO SLIDES
DELETE FROM `hero_slides`;
INSERT INTO `hero_slides` (`id`, `label`, `headline_lines_json`, `supporting`, `cta_label`, `cta_href`, `secondary_cta_label`, `secondary_cta_href`, `image_desktop`, `image_mobile`, `video_src`, `sort_order`, `is_active`) VALUES
(1, 'PPF • DETAILING • CAR CARE', '["YOUR CAR.", "REDEFINED."]', 'Jammu''s premier automotive detailing studio for paint protection and precision care.', 'BOOK YOUR SERVICE →', '/book', 'EXPLORE SERVICES →', '/services', '/assets/img/hero/slide-1-desktop.jpg', '/assets/img/hero/slide-1-mobile.jpg', NULL, 1, 1),
(2, 'PAINT PROTECTION FILM', '["PROTECT WHAT", "YOU DRIVE."]', 'Self-healing optical armor safeguarding original factory paint from highway debris.', 'EXPLORE PPF →', '/services/ppf', 'GET PPF QUOTE →', '/book?service=ppf', '/assets/img/hero/slide-2-desktop.jpg', '/assets/img/hero/slide-2-mobile.jpg', NULL, 2, 1),
(3, 'BIKE DETAILING', '["TWO WHEELS.", "FULL ATTENTION."]', 'Dedicated precision care tailored for superbikes and everyday performance rides.', 'DETAIL MY BIKE →', '/services/bike-detailing', 'BOOK BIKE SERVICE →', '/book?service=bike-detailing', '/assets/img/hero/slide-3-desktop.jpg', '/assets/img/hero/slide-3-mobile.jpg', NULL, 3, 1),
(4, 'CERAMIC WASH', '["CLEAN. GLOSS.", "DRIVE."]', 'Hydrophobic foam bath and gloss enhancement without swirl marks.', 'BOOK A WASH →', '/book?service=ceramic-wash', 'LEARN MORE →', '/services/ceramic-wash', '/assets/img/hero/slide-4-desktop.jpg', '/assets/img/hero/slide-4-mobile.jpg', NULL, 4, 1),
(5, 'STEAM & INTERIOR CARE', '["DETAILING WITHOUT", "COMPROMISE."]', 'Hyper-clean cabin sterilization and showroom-grade surface finishing.', 'EXPLORE SERVICES →', '/services', 'BOOK APPOINTMENT →', '/book', '/assets/img/hero/slide-5-desktop.jpg', '/assets/img/hero/slide-5-mobile.jpg', NULL, 5, 1);

-- 3. SERVICES (All 15 services)
DELETE FROM `services`;
INSERT INTO `services` (`id`, `slug`, `name`, `category`, `vehicle_types`, `is_major`, `module`, `short_desc`, `intro_lead`, `intro_body`, `included_json`, `benefits_json`, `who_json`, `process_json`, `hero_slides_json`, `related_slugs_json`, `duration_note`, `sort_order`, `is_active`, `seo_title`, `seo_description`) VALUES
-- 1. PPF
(1, 'ppf', 'PAINT PROTECTION FILM', 'protect', 'car', 1, 'ppf', 
 'Self-healing aliphatic polyurethane film designed to guard against stone chips, scratches, road debris, and UV oxidation.',
 'DEFEND YOUR ORIGINAL FACTORY FINISH WITH INVISIBLE, SELF-HEALING ARMOR.',
 'Paint Protection Film (PPF) is the ultimate defense system for your vehicle''s exterior. Engineered from optically clear, thermoplastic polyurethane, our film creates an impenetrable barrier between Jammu''s challenging road conditions and your factory clear coat. Minor scratches and swirl marks disappear automatically with ambient heat, preserving true showroom gloss year after year.',
 '["Precision Edge-Wrapped Installation", "Self-Healing Top Coat Technology", "High-Impact Stone Chip & Scratch Shield", "UV & Oxidation Degradation Blocker", "Hydrophobic Stain & Chemical Resistance", "Complete Multi-Stage Pre-Install Decontamination"]',
 '[{"title":"Scratch Resistance","desc":"Self-healing polymer matrix flows back to smooth state when exposed to engine or solar heat."},{"title":"Stone-Chip Defense","desc":"High-tensile polyurethane absorbs gravel and high-velocity road debris on highway corridors."},{"title":"UV & Chemical Shield","desc":"Eliminates paint yellowing, etching from bird droppings, acid rain, and harsh environmental contaminants."},{"title":"Original Paint Value","desc":"Protects OEM paint intact, directly safeguarding the vehicle resale valuation."}]',
 '[{"title":"New Vehicle Owners","desc":"Install immediately from the showroom floor to lock in factory paint condition."},{"title":"Highway Commuters","desc":"Crucial for vehicles navigating Jammu-Srinagar or Jammu-Delhi highways with frequent gravel exposure."},{"title":"Enthusiast & Luxury Cars","desc":"Preserve mirror-gloss exotic finishes against daily environmental wear."}]',
 '[{"step":"01","title":"Vehicle Inspection","desc":"Comprehensive paint thickness analysis and surface defect cataloguing."},{"step":"02","title":"Surface Preparation","desc":"Multi-stage foam wash, iron decontamination, clay bar, and single/two-stage paint correction."},{"step":"03","title":"Film Installation","desc":"Computer-cut patterns and slip-solution positioning in a dust-free bay."},{"step":"04","title":"Edge Finishing","desc":"Seamless panel wrapping around edges, badges, and body seams for invisible coverage."},{"step":"05","title":"Quality Inspection","desc":"High-intensity lighting audit to ensure zero bubbles, lift lines, or stretching defects."},{"step":"06","title":"Final Handover","desc":"Curing period followed by sealant application and comprehensive warranty registration."}]',
 '[{"title":"EXTERIOR FULL BODY","caption":"Seamless full-body self-healing film installation."},{"title":"INSTALLATION CLOSE-UP","caption":"Micro-squeegee precision tucking along body panels."},{"title":"HYDROPHOBIC WATER BEADING","caption":"Instant water repulsion on cured protected clear coat."}]',
 '["ceramic-coating", "full-car-detailing", "maintenance-wash"]',
 '2 - 3 Days', 1, 1,
 'Paint Protection Film (PPF) in Jammu | The Drive Clinic',
 'Defend your vehicle with high-grade self-healing Paint Protection Film (PPF) in Nanak Nagar, Jammu. Protect against stone chips, scratches, and UV damage.'),

-- 2. CERAMIC COATING
(2, 'ceramic-coating', 'CERAMIC COATING', 'protect', 'car,bike', 1, NULL,
 'Nanotechnology liquid glass matrix that permanently bonds to paint, delivering intense candy-like gloss and extreme water beading.',
 'ULTRA-DENSE 9H MOLECULAR PROTECTION FOR CANDY GLOSS AND UNMATCHED HYDROPHOBICS.',
 'Our ceramic coating formulas penetrate the microscopic valleys of your vehicle''s clear coat, creating a sacrificial, glass-like quartz matrix. This chemical bond produces intense optical depth, self-cleaning water repulsion, and high resistance to chemical stains, tree sap, and Jammu''s road grime.',
 '["Paint Decontamination & Clay Bar Treatment", "Machine Cut & Polish Paint Correction", "Solvent Surface Wipedown", "9H Nano Ceramic Glass Coating", "Glass & Wheel Rim Coating Protection", "Infrared Heat Curing Cycle"]',
 '[{"title":"Candy Optical Gloss","desc":"Deepens paint color saturation and highlights metallic flakes with mirror clarity."},{"title":"Extreme Water Beading","desc":"Water and road grime effortlessly sheet off surfaces, creating a self-cleaning effect."},{"title":"Thermal & Chemical Barrier","desc":"Shields against high temperatures, industrial fallout, and corrosive road grime."},{"title":"Effortless Maintenance","desc":"Reduces wash times by half and eliminates the need for periodic wax applications."}]',
 '[{"title":"Daily Driven Cars","desc":"Keep your vehicle cleaner for longer periods between maintenance washes."},{"title":"Black & Dark Paint Finishes","desc":"Prevent the hazy oxidation and micro-swirls common in dark vehicle paint."},{"title":"Motorcycles & Superbikes","desc":"Protect fuel tanks, fairings, and metallic components from fuel splashes and weathering."}]',
 '[{"step":"01","title":"Paint Decontamination","desc":"Stripping old waxes, iron fallout, and road tar particles."},{"step":"02","title":"Paint Correction","desc":"Eliminating up to 90% of swirl marks and clear coat scratches."},{"step":"03","title":"Panel Prep","desc":"Alcohol wipe down to reveal bare, pristine clear coat for bonding."},{"step":"04","title":"Coating Application","desc":"Cross-hatch panel-by-panel application using lint-free suede applicators."},{"step":"05","title":"Leveling & Buffing","desc":"Precise flash-time monitoring and dual-microfiber leveling."},{"step":"06","title":"IR Curing","desc":"Infrared lamp baking to accelerate molecular cross-linking."}]',
 '[{"title":"MIRROR PAINT REFLECTION","caption":"Flawless reflection after multi-stage ceramic application."},{"title":"WATER BEADING MATRIX","caption":"Dense hydrophobic contact angle on treated paint."}]',
 '["maintenance-wash", "polishing-waxing", "ppf"]',
 '1 - 2 Days', 2, 1,
 'Ceramic Coating in Jammu | 9H Paint Protection | The Drive Clinic',
 'Long-lasting 9H ceramic coating in Jammu. Mirror finish, hydrophobic water beading, and UV protection for cars and bikes.'),

-- 3. BIKE DETAILING
(3, 'bike-detailing', 'BIKE DETAILING', 'detail', 'bike', 1, 'bike',
 'Complete multi-step deep cleaning, mechanical component degreasing, alloy restoration, and protective sealant for motorcycles.',
 'TWO WHEELS. FULL ATTENTION. PRECISION REJUVENATION FOR EVERY RIDE.',
 'Motorcycles expose their mechanical heart directly to road elements. Our Bike Detailing program treats every exposed inch — from delicate chrome headers and engine cooling fins to painted fuel tanks and chain assemblies — with pH-neutral cleansers, precision steam, metal polish, and protective sealants.',
 '["pH-Neutral Foam Pre-Wash & Hand Clean", "Engine Block & Cooling Fin Degreasing", "Chain Clean, Degrease & High-Speed Lube", "Wheel Rim, Brake Caliper & Spoke Deep Clean", "Exhaust Header Pipe Stain Removal & Metal Polish", "Fuel Tank & Fairing Machine Polish with Sealant"]',
 '[{"title":"Mechanical Degreasing","desc":"Eliminates baked-on chain oil, road tar, and engine grit from tight crevices."},{"title":"Chrome & Metal Polish","desc":"Restores brightwork, silencers, and aluminum forks to their original shine."},{"title":"Paint Correction","desc":"Removes rider jacket scratches and zipper marks from the fuel tank."},{"title":"Corrosion Protection","desc":"Seals electrical connectors and exposed metal against monsoon humidity and winter salt."}]',
 '[{"title":"Superbike & Cruiser Riders","desc":"Show-ready aesthetics for premium superbikes, cruisers, and retro classics."},{"title":"Daily Commuter Motorcycles","desc":"Thorough de-grime and chain maintenance after heavy monsoon or winter runs."},{"title":"Touring & Adventure Bikes","desc":"Full restoration following Leh-Ladakh or mountain expeditions."}]',
 '[{"step":"01","title":"Cool-Down & Pre-Rinse","desc":"Allowing engine to reach ambient temperature followed by safe low-pressure rinse."},{"step":"02","title":"Chemical De-greasing","desc":"Targeted solvent brush agitation across sprockets, chain, and crankcase."},{"step":"03","title":"Snow Foam Bath","desc":"Lifting surface dust without abrasive contact."},{"step":"04","title":"Detailing Brushes","desc":"Intricate cleaning of handlebar controls, radiator fins, and swingarms."},{"step":"05","title":"Tank & Panel Polish","desc":"Precision rotary polish on painted fairings and tank contours."},{"step":"06","title":"Dress & Protect","desc":"Non-greasy tyre dressing, chain lubrication, and hydrophobic sealant spray."}]',
 '[{"title":"SUPERBIKE FULL PROFILE","caption":"Pristine finish on sports motorcycle fairings and wheels."},{"title":"ENGINE & EXHAUST CLOSE-UP","caption":"Restored header pipes and immaculate crankcase."},{"title":"FINISHED TANK GLOSS","caption":"Swirl-free high gloss on motorcycle fuel tank."}]',
 '["ceramic-wash", "engine-detailing", "tyre-rim-care"]',
 '3 - 5 Hours', 3, 1,
 'Bike Detailing in Jammu | Superbike & Motorcycle Care | The Drive Clinic',
 'Precision motorcycle detailing in Jammu. Engine degreasing, chain care, chrome polish, and tank paint correction in Nanak Nagar.'),

-- 4. FULL CAR DETAILING
(4, 'full-car-detailing', 'FULL CAR DETAILING', 'detail', 'car', 1, NULL,
 'Bumper-to-bumper bumper restoration combining multi-stage exterior paint correction with deep interior cabin decontamination.',
 'TOTAL VEHICLE RESTORATION INSIDE AND OUT FOR A TRUE SHOWROOM RESET.',
 'Our signature Full Car Detailing package is a masterclass in comprehensive vehicle care. We restore paint depth, polish cloudy trim, steam-sterilize cabin fabrics, condition leather upholstery, and clean intricate vents and emblems.',
 '["Multi-Stage Snow Foam & Two-Bucket Exterior Hand Wash", "Paint Decontamination & Clay Treatment", "Two-Stage Compound Cut & Finish Machine Polish", "Interior Deep Steam Cleaning & Extraction", "Leather Cleanse & Nourishing Conditioner", "Engine Bay Dry Steam Cleaning & Dressing", "Alloy Wheels, Wheel Wells & Tyre Dressing"]',
 '[{"title":"Complete Exterior Reset","desc":"Eliminates light scratches, swirl marks, and paint dullness."},{"title":"Deep Sanitized Cabin","desc":"Steam extraction kills 99.9% of bacteria, dust mites, and odors in fabrics."},{"title":"Conditioned Surfaces","desc":"Prevents leather cracking and dashboard UV fading with matte finish conditioners."},{"title":"360-Degree Attention","desc":"No emblem, door jamb, or foot pedal is overlooked."}]',
 '[{"title":"Pre-Sale Preparation","desc":"Significantly increases used car valuation prior to private sale or trade-in."},{"title":"Annual Deep Clean","desc":"The perfect yearly reset for family vehicles and daily commuters."},{"title":"Pre-Owned Car Refresh","desc":"Erase previous owner history and restore true factory freshness."}]',
 '[{"step":"01","title":"Interior First","desc":"Thorough blowout, high-power vacuuming, steam sterilization, and extraction."},{"step":"02","title":"Engine Bay Detail","desc":"Dry steam cleaning of engine plastics, battery terminals, and under-bonnet lines."},{"step":"03","title":"Exterior Decon","desc":"Foam soak, bug removal, iron fallout breakdown, and fine-grade clay bar."},{"step":"04","title":"Two-Stage Polish","desc":"Heavy cut compound followed by micro-finish jewel polishing."},{"step":"05","title":"Protection Layer","desc":"Application of 6-month synthetic ceramic paint sealant."},{"step":"06","title":"Final Inspection","desc":"Streak-free glass cleaning, tyre dressing, and aroma neutralization."}]',
 '[{"title":"COMPLETE TRANSFORMATION","caption":"Full exterior and interior transformation."},{"title":"INTERIOR COCKPIT","caption":"Immaculate leather seats and pristine center console."}]',
 '["ceramic-coating", "maintenance-wash", "engine-detailing"]',
 '1 Day', 4, 1,
 'Full Car Detailing in Jammu | Complete Interior & Exterior Reset',
 'Complete bumper-to-bumper car detailing in Nanak Nagar, Jammu. Paint correction, steam interior extraction, engine bay care.'),

-- 5. CERAMIC WASH
(5, 'ceramic-wash', 'CERAMIC WASH', 'clean', 'car,bike', 1, 'ceramic-wash',
 'High-lubricity pH-neutral foam wash infused with ceramic SiO2 polymers that cleans while topping up gloss and water repellency.',
 'CLEAN. GLOSS. DRIVE. THE ULTIMATE HYDROPHOBIC MAINTENANCE WASH.',
 'Unlike standard car washes that strip wax and induce micro-scratches, our Ceramic Wash employs ultra-slick pH-neutral foam and genuine SiO2 ceramic booster additives. As dirt is safely lifted away, a protective molecular layer is deposited, rejuvenating gloss and repelling road dirt.',
 '["High-Pressure Touchless Pre-Rinse", "Thick SiO2 Infused Ceramic Snow Foam Bath", "Two-Bucket Microfiber Hand Wash with Grit Guards", "Deep Wheel Barrel, Lug Nut & Caliper Face Cleaning", "De-ionized Filtered Water Rinse", "Blow Dry & Ultra-Plush Microfiber Drying", "Spray Ceramic Gloss Top-Up Coat"]',
 '[{"title":"Zero Swirl Mark Guarantee","desc":"Grit-guard buckets and high-lubricity foam prevent wash-induced spiderweb scratches."},{"title":"Instant Hydrophobic Boost","desc":"Leaves paint sheeting water instantly with intense water beading."},{"title":"Gloss Amplification","desc":"SiO2 polymers enhance metallic sparkle and deep wet gloss reflection."},{"title":"Safe on Coatings & Films","desc":"Specially formulated to extend the lifespan of PPF and ceramic coatings."}]',
 '[{"title":"Coated & Wrapped Vehicles","desc":"The ideal routine wash to maintain ceramic coatings and PPF."},{"title":"Enthusiasts Seeking Extra Gloss","desc":"Achieve a freshly detailed shine in a fraction of the time."},{"title":"Regular Vehicle Maintenance","desc":"Keep your car looking pristine without damaging delicate clear coats."}]',
 '[{"step":"01","title":"Pre-Wash Soak","desc":"Loosening heavy grit and road film with contactless foam."},{"step":"02","title":"Wheel Treatment","desc":"Cleaning brake dust from barrels, faces, and tyre walls."},{"step":"03","title":"Hand Wash","desc":"Soft microfiber mitts washed per panel using grit guards."},{"step":"04","title":"Spot-Free Rinse","desc":"High-pressure clean water rinse to flush away all detergents."},{"step":"05","title":"Air Cannon Drying","desc":"Forced warm air blows water out of mirrors, grilles, and trim lines."},{"step":"06","title":"Ceramic Gloss Mist","desc":"Wipedown with SiO2 detailer for deep shine."}]',
 '[{"title":"FOAM BATH","caption":"Dense ceramic snow foam encapsulating road dirt."},{"title":"FINISHED GLOSS","caption":"Ultra-reflective slick gloss finish after ceramic wash."}]',
 '["maintenance-wash", "polishing-waxing", "ceramic-coating"]',
 '45 - 60 Mins', 5, 1,
 'Ceramic Car Wash in Jammu | The Drive Clinic',
 'Premium SiO2 ceramic foam wash in Nanak Nagar, Jammu. Safe, swirl-free, high-gloss hydrophobic car and bike wash.'),

-- 6. STEAM WASH
(6, 'steam-wash', 'STEAM WASH', 'clean', 'car', 1, 'steam-wash',
 'High-temperature vapor steam technology that sanitizes surfaces, dissolves stubborn grease, and purges hidden cabin contaminants.',
 'DEEP CLEAN. PRECISE CARE. 100% CHEMICAL-CONSCIOUS SANITIZATION.',
 'Our commercial-grade vapor steam generators deliver 160°C dry steam that instantly breaks down grease, spilled liquids, smoke residues, and allergens without soaking your interior upholstery. It reaches deep into air conditioning ducts and seat stitching for complete sterilization.',
 '["High-Temperature 160°C Dry Vapor Steam Cleaning", "Air Conditioning Vent & Duct Disinfection", "Fabric Seat & Carpet Steam Extraction", "Dashboard, Console & Door Panel Purge", "Door Jambs, Fuel Flap & Boot Gutter Steam Clean", "All-Weather Floor Mat Sanitization"]',
 '[{"title":"99.9% Pathogen Elimination","desc":"Steam heat kills bacteria, mold spores, viruses, and allergen build-up."},{"title":"Odor Source Neutralization","desc":"Destroys odor-causing bacteria rather than masking with perfumed sprays."},{"title":"Zero Harsh Residue","desc":"Chemical-free cleaning safe for children, pets, and allergy sufferers."},{"title":"Rapid Cabin Dry Time","desc":"Low moisture steam ensures seats are dry and ready for use in under 30 minutes."}]',
 '[{"title":"Families with Kids & Pets","desc":"Ensure a sanitized, chemical-free cabin interior."},{"title":"Vehicles with Odors or Dampness","desc":"Eradicate damp monsoon smells or air conditioner mustiness."},{"title":"Used Car Purchases","desc":"Complete hygienic reset of all interior touchpoints."}]',
 '[{"step":"01","title":"Dry Vacuum","desc":"Deep suction removal of loose debris from carpet and seats."},{"step":"02","title":"AC Vent Purge","desc":"Injecting high-temperature dry steam through ventilation lines."},{"step":"03","title":"Upholstery Steam Clean","desc":"Steam agitation across seat fabrics, seams, and headrests."},{"step":"04","title":"Trim & Crevice Purge","desc":"Steam wand lifting grime from cup holders and switches."},{"step":"05","title":"Extraction Wipedown","desc":"Microfiber capture of lifted oils and moisture."},{"step":"06","title":"Cabin Air Sanitization","desc":"Final air circulation cycle ensuring fresh cabin atmosphere."}]',
 '[{"title":"STEAM INJECTION","caption":"160°C dry steam purging ventilation vents."},{"title":"CABIN REJUVENATION","caption":"Sterilized, residue-free dashboard and seating."}]',
 '["interior-deep-clean", "odor-removal", "maintenance-wash"]',
 '1 - 2 Hours', 6, 1,
 'Steam Car Wash & Interior Sanitization in Jammu | The Drive Clinic',
 'High-temperature 160°C dry steam car cleaning in Jammu. Sanitize AC vents, kill bacteria, and deep clean upholstery.'),

-- 7. MAINTENANCE WASH
(7, 'maintenance-wash', 'MAINTENANCE WASH', 'clean', 'car,bike', 1, 'maintenance',
 'Routine scheduled upkeep wash designed for coated, wrapped, and clean vehicles to preserve pristine exterior condition.',
 'KEEP THE SHINE ALIVE. FAST, CONSISTENT, SHOWROOM-GRADE CARE.',
 'Designed for vehicle owners who demand consistent perfection. Our Maintenance Wash is an efficient, high-standard maintenance routine performed bi-weekly or monthly to ensure dust, road film, and brake grime never build up to hazardous levels.',
 '["Two-Bucket Scratch-Free Exterior Wash", "Wheel Faces & Tyre Cleaning", "Door Jamb Wipedown", "High-Power Interior Vacuum & Foot Mat Cleaning", "Streak-Free Glass Cleaning (Inside & Out)", "Quick Synthetic Spray Sealant Boost"]',
 '[{"title":"Preserves Detailing & Coatings","desc":"Specially calibrated soaps that nourish existing PPF and ceramic layers."},{"title":"Prevents Long-Term Damage","desc":"Stops road salts and bird droppings from etching your clear coat."},{"title":"Time Efficient","desc":"Fast turnaround without cutting corners on detailing quality."},{"title":"Recurring Schedule Savings","desc":"Keep your vehicle spotless effortlessly with pre-scheduled slots."}]',
 '[{"title":"Coated / PPF Protected Vehicles","desc":"The recommended routine care cycle for protected vehicles."},{"title":"Executive & Daily Drivers","desc":"Maintain an impeccable professional image year-round."},{"title":"Enthusiasts on a Routine","desc":"Book every 2 or 4 weeks for guaranteed priority slots."}]',
 '[{"step":"01","title":"Foam Pre-Rinse","desc":"Lifting ambient dust without scrubbing."},{"step":"02","title":"Hand Wash","desc":"Plush microfiber mitts gliding on lubricated shampoo."},{"step":"03","title":"Interior Vacuum","desc":"Quick cabin refresh including foot mats and seats."},{"step":"04","title":"Glass Clean","desc":"Crystal-clear optical glass wipedown."},{"step":"05","title":"Sealant Spritz","desc":"Quick shine boost to enhance paint slickness."}]',
 '[{"title":"ROUTINE FOAM WASH","caption":"Fast, gentle snow foam maintenance wash."},{"title":"CRISP CLEAN CAR","caption":"Spotless finish ready for the road."}]',
 '["ceramic-wash", "interior-deep-clean", "polishing-waxing"]',
 '30 - 45 Mins', 7, 1,
 'Maintenance Car Wash in Jammu | Routine Vehicle Care | The Drive Clinic',
 'Scheduled maintenance car and bike wash in Nanak Nagar, Jammu. Protect your ceramic coating and PPF with regular upkeep.'),

-- 8. ENGINE DETAILING
(8, 'engine-detailing', 'ENGINE DETAILING', 'detail', 'car,bike', 1, NULL,
 'Safe dry steam cleaning, grease removal, and OEM satin dressing for under-hood mechanical compartments.',
 'RESTORE THE POWERHOUSE. CLEAN, DEGREASED, AND DRESSED TO OEM STANDARDS.',
 'Your engine bay collects grease, leaves, road grime, and dust. We use controlled low-moisture steam and specialized electrical-safe cleaners to degrease engine covers, wiring looms, and firewall panels, finishing with a heat-resistant, anti-static satin dressing.',
 '["Battery & Electrical Sensitive Component Masking", "Heavy-Duty Oil & Grease Breakdown", "Low-Moisture Steam Jet Agitation", "Plastic, Rubber & Hose Rejuvenation", "Anti-Static Heat-Resistant Satin Dressing"]',
 '[{"title":"Heat Dissipation","desc":"Removing caked dirt and sludge allows components to run cooler."},{"title":"Leak Detection","desc":"A clean engine bay makes identifying minor fluid leaks instantaneous."},{"title":"Prevents Rubber Cracking","desc":"Nourishing dressings protect vital hoses and wiring sheaths from dry rot."},{"title":"Resale Appeal","desc":"A pristine engine bay is the top indicator of a meticulously cared-for vehicle."}]',
 '[{"title":"Performance & Luxury Vehicles","desc":"Showcase your powerplant under the bonnet."},{"title":"Pre-Purchase / Pre-Sale Cars","desc":"Impress buyers and inspection mechanics."},{"title":"Older or Dusty Engines","desc":"Remove years of caked grime safely."}]',
 '[{"step":"01","title":"Inspection & Masking","desc":"Covering sensitive electronics, alternator, and air intakes."},{"step":"02","title":"Degreaser Soak","desc":"Citrus degreaser breaking down road oil."},{"step":"03","title":"Steam Jet Cleaning","desc":"Controlled vapor lifting grease from tight components."},{"step":"04","title":"Compressed Air Dry","desc":"Blowing all moisture from spark plug wells and plugs."},{"step":"05","title":"Satin Dressing","desc":"Applying non-greasy, non-conductive OEM satin protective finish."}]',
 '[{"title":"ENGINE BEFORE & AFTER","caption":"Immaculate under-hood restoration and satin dressing."}]',
 '["full-car-detailing", "ceramic-wash", "steam-wash"]',
 '1 - 2 Hours', 8, 1,
 'Engine Bay Detailing in Jammu | Safe Steam Cleaning | The Drive Clinic',
 'Professional engine bay cleaning in Jammu. Safe dry-steam grease removal and satin dressing in Nanak Nagar.'),

-- 9. INTERIOR DEEP CLEAN
(9, 'interior-deep-clean', 'INTERIOR DEEP CLEAN', 'detail', 'car', 1, NULL,
 'Comprehensive cabin detailing featuring heated carpet extraction, stain removal, leather treatment, and roof-liner cleansing.',
 'STERILIZED LUXURY. COMPREHENSIVE CABIN DEEP-CLEANSE AND STAIN EXTRACTION.',
 'Spills, dust, makeup stains, and perspiration accumulate in fabric weaves and leather grain. Our Interior Deep Clean disassembles seat rails if required, cleans roof liners, shampoos floor carpets with heated extraction, and conditions all surfaces to a factory-fresh matte sheen.',
 '["High-Power Suction Pre-Vacuum", "Roof Liner & Sun Visor Delicates Cleaning", "Hot Water Upholstery & Carpet Extraction", "Leather Scrubbing & Conditioner Nourishing", "Seat Belt, Trunk & Spare Wheel Well Deep Clean", "Glass & Mirror Crystal Clean"]',
 '[{"title":"Stain & Spill Removal","desc":"Lifts coffee, mud, grease, and dye transfers from fabric and leather."},{"title":"Allergen Free Interior","desc":"Extracts deeply embedded dust mites, pet dander, and fine dust."},{"title":"Restored Leather Softness","desc":"Replenishes natural oils to prevent dry leather cracking."},{"title":"Factory Matte Look","desc":"Zero greasy residue; leaves surfaces with a refined OEM appearance."}]',
 '[{"title":"Family Vehicles","desc":"Erase children''s snack spills, drink rings, and floor mud."},{"title":"Pre-Owned Purchases","desc":"Sanitize and refresh a newly acquired used car."},{"title":"Monsoon Recovery","desc":"Eliminate damp footwear grime and mold buildup."}]',
 '[{"step":"01","title":"Trash & Mat Removal","desc":"Emptying cabin and high-pressure scrubbing of floor mats."},{"step":"02","title":"Roof Liner Treatment","desc":"Gentle dry foam cleaning preventing sagging."},{"step":"03","title":"Seats & Carpet Extraction","desc":"Hot water injection and simultaneous high-power vacuum extraction."},{"step":"04","title":"Detailing Brushes","desc":"Cleaning vents, knobs, buttons, and door panels."},{"step":"05","title":"Leather Conditioning","desc":"Nourishing treatment massaged into leather seats."},{"step":"06","title":"De-humidification Dry","desc":"Complete cabin moisture drying and final check."}]',
 '[{"title":"CABIN RECOVERY","caption":"Stain-free pristine leather and carpet upholstery."}]',
 '["steam-wash", "odor-removal", "full-car-detailing"]',
 '3 - 5 Hours', 9, 1,
 'Interior Car Detailing & Deep Cleaning in Jammu | The Drive Clinic',
 'Deep interior car cleaning in Jammu. Heated carpet extraction, leather conditioning, roof liner care in Nanak Nagar.'),

-- 10. POLISHING & WAXING
(10, 'polishing-waxing', 'POLISHING & WAXING', 'detail', 'car,bike', 1, NULL,
 'Precision single-stage machine gloss enhancement followed by an ultra-slick carnauba or ceramic sealant top coat.',
 'BRILLIANT SHINE AND ENHANCED REFLECTION WITHOUT EXTENSIVE COMPOUNDING.',
 'When your paint has minor haze or light swirl marks, a single-stage machine polish revitalizes the clear coat by eliminating oxidation and micro-imperfections. We follow with a hand-buffed carnauba-polymer blend for high water beading and mirror finish.',
 '["Decontamination Foam Wash & Clay Bar", "Single-Stage Machine Dual-Action Polish", "Paint Glaze Clarity Enhancer", "Carnauba / Polymer Paste Sealant Application", "Chrome & Exterior Plastic Gloss Dressing"]',
 '[{"title":"Restores Paint Depth","desc":"Eliminates surface haze and restores rich color depth."},{"title":"Removes Light Swirls","desc":"Buffs away fine wash scratches and micro-spiderwebbing."},{"title":"3 to 6 Months Protection","desc":"Wax shield prevents environmental staining and UV oxidation."},{"title":"Cost-Effective Enhancement","desc":"Dramatic visual upgrade for vehicles not requiring multi-stage correction."}]',
 '[{"title":"Occasional Weekend Drivers","desc":"Get an intense show-car shine for special occasions."},{"title":"Vehicles with Good Clear Coat","desc":"Maintain gloss without aggressive paint removal."},{"title":"Budget-Conscious Owners","desc":"High-impact aesthetic restoration at an accessible price."}]',
 '[{"step":"01","title":"Paint Decontamination","desc":"Clay bar smoothing clear coat."},{"step":"02","title":"Masking Trim","desc":"Protecting unpainted plastic trims with automotive tape."},{"step":"03","title":"Dual-Action Machine Polish","desc":"Fine polish pad polishing clear coat."},{"step":"04","title":"Wax Application","desc":"Hand-applying premium sealant."},{"step":"05","title":"Microfiber Buff","desc":"Buffing to ultra-slick mirror finish."}]',
 '[{"title":"MACHINE POLISH","caption":"Single-stage machine polishing enhancing clear coat depth."}]',
 '["ceramic-wash", "full-car-detailing", "maintenance-wash"]',
 '2 - 3 Hours', 10, 1,
 'Car Polishing & Waxing in Jammu | Paint Enhancement | The Drive Clinic',
 'Machine polishing and waxing in Jammu. Remove light swirls and boost paint gloss with premium sealants in Nanak Nagar.'),

-- 11. TYRE & RIM CARE
(11, 'tyre-rim-care', 'TYRE & RIM CARE', 'detail', 'car,bike', 1, NULL,
 'Deep iron fallout removal from alloy wheel barrels, brake caliper cleaning, and long-lasting non-sling tyre dressing.',
 'WHEEL BARREL PURITY. METALLIC GLOSS AND DEEP SATIN TYRE NOURISHMENT.',
 'Brake dust contains sintered metallic iron that burns into wheel clear coats if neglected. Our dedicated Tyre & Rim Care service dissolves metallic fallout with color-changing iron decon gels, scrubs inner barrels, cleans wheel arches, and conditions rubber against UV browning.',
 '["Acid-Free Color-Changing Iron Fallout Breakdown", "Wheel Barrel & Face Agitation with Woolies", "Brake Caliper Deep Scrub & Rinse", "Wheel Arch Pressure Clean & Blackout Dressing", "Sling-Free Hydrophobic Tyre Dressing"]',
 '[{"title":"Stops Brake Dust Pitting","desc":"Prevents corrosive brake dust from permanently pitting alloy wheels."},{"title":"Restores OEM Alloy Finish","desc":"Removes brown road film and yellow oxidation from wheels."},{"title":"Deep Black Rubber Finish","desc":"Tyre dressing nourishes rubber, preventing dry rot and brown blooming."},{"title":"Sling-Free Formula","desc":"Dressing bonds to rubber and won''t spray onto your clean paintwork when driving."}]',
 '[{"title":"Performance Cars with Big Brakes","desc":"Tackle the heavy brake dust produced by sports sedans and SUVs."},{"title":"Diamond-Cut & Painted Alloys","desc":"Preserve delicate factory clear coat on alloy rims."},{"title":"Off-Road & 4x4 Enthusiasts","desc":"Purge dried mud and gravel from wheel arches and rim wells."}]',
 '[{"step":"01","title":"Cool Down","desc":"Ensuring brake rotors are cool before chemical application."},{"step":"02","title":"Iron Decon Gel","desc":"Spraying iron-reactive cleanser to dissolve metallic dust."},{"step":"03","title":"Agitation","desc":"Using dedicated soft-bristle brushes for lug nuts, faces, and barrels."},{"step":"04","title":"High-Pressure Flush","desc":"Rinsing dissolved contaminants away."},{"step":"05","title":"Tyre Nourishment","desc":"Applying dual-coat hydrophobic satin tyre dressing."}]',
 '[{"title":"WHEEL DETAIL","caption":"Sparkling clean alloy barrels and satin dressed tyre."}]',
 '["ceramic-wash", "bike-detailing", "full-car-detailing"]',
 '45 - 60 Mins', 11, 1,
 'Alloy Wheel & Tyre Detailing in Jammu | The Drive Clinic',
 'Brake dust iron removal, wheel barrel cleaning, and non-sling tyre dressing in Nanak Nagar, Jammu.'),

-- 12. ODOR REMOVAL
(12, 'odor-removal', 'ODOR REMOVAL', 'detail', 'car', 1, NULL,
 'Advanced molecular ozone shock treatment and enzymatic fogging that permanently destroys smoke, dampness, and bacterial smells.',
 'PERMANENT MOLECULAR DESTRUCTION OF EMBEDDED CABIN ODORS.',
 'Masking bad odors with air fresheners only lasts days. Our Odor Removal system uses medical-grade Ozone (O3) generators and thermal dry fogging to break down organic odor molecules at the molecular level, sterilizing deep seat cushions and HVAC ductwork permanently.',
 '["Cabin Pre-Clean & Source Identification", "Enzymatic Organic Waste Neutralization", "Ozone Shock Treatment Generator Cycle", "AC Ductwork Thermal Antimicrobial Fogging", "Residual Gas Evacuation & Natural Air Refresh"]',
 '[{"title":"Destroys, Doesn''t Mask","desc":"Ozone oxidizes odor-causing compounds into harmless water and oxygen."},{"title":"Reaches Hidden Areas","desc":"Gas penetrates headliners, foam seat cushions, and ventilation tunnels."},{"title":"Eliminates Smoke & Mildew","desc":"Highly effective against cigarette smoke, damp monsoon mildew, and pet odors."},{"title":"Hygienic Cabin Space","desc":"Leaves the car smelling crisp, fresh, and chemically neutral."}]',
 '[{"title":"Vehicles Exposed to Tobacco Smoke","desc":"Permanently remove lingering nicotine and tar odor."},{"title":"Monsoon Damp / Mildew Smells","desc":"Kill the fungus growing inside AC evaporators and damp footwells."},{"title":"Pet Owners & Spill Accidents","desc":"Eradicate spilled milk, food accidents, or pet smell."}]',
 '[{"step":"01","title":"Source Elimination","desc":"Locating and cleaning the physical root cause of the odor."},{"step":"02","title":"Cabin Pre-Conditioning","desc":"Running climate control on recirculate."},{"step":"03","title":"Ozone Generator Deployment","desc":"Timed high-output O3 gas flood penetrating cabin fabrics."},{"step":"04","title":"Thermal Fogging","desc":"Disinfectant vapor run through air conditioning ducts."},{"step":"05","title":"Air Flush","desc":"High-volume fan evacuation leaving clean, sterilized air."}]',
 '[{"title":"OZONE SANITIZATION","caption":"Ozone generator eliminating odor molecules inside cabin."}]',
 '["steam-wash", "interior-deep-clean", "maintenance-wash"]',
 '1 - 2 Hours', 12, 1,
 'Car Odor Removal & Ozone Sanitization in Jammu | The Drive Clinic',
 'Permanent car odor removal in Jammu. Ozone treatment and AC duct sanitization in Nanak Nagar.'),

-- 13. INTERIOR DRY CLEANING (Minor)
(13, 'interior-dry-cleaning', 'INTERIOR DRY CLEANING', 'detail', 'car', 0, NULL,
 'Low-moisture foam cleaning for delicate fabrics, dashboard surfaces, and floor carpets.',
 'QUICK-DRY FABRIC AND VINYL CLEANSE FOR ROUTINE CABIN REFRESHES.',
 'For vehicles needing a fast cabin spruce-up without wet extraction downtime, our Interior Dry Cleaning utilizes specialized low-moisture foaming agents to lift dust and skin oils from seats, dashboards, and door cards.',
 '["Cabin High-Power Vacuum", "Dry Foam Fabric Cleansing", "Dashboard & Center Console Wipedown", "Door Trim Detailing", "Glass Clear Finish"]',
 '[{"title":"Rapid Turnaround","desc":"Cabin is completely dry and ready to drive within minutes."},{"title":"Removes Daily Dust","desc":"Lifts city dust and finger marks from all interior touchpoints."}]',
 '[{"title":"Fleet & Daily Commuters","desc":"Quick cabin freshening during working hours."}]',
 '[{"step":"01","title":"Vacuum","desc":"Vacuuming all surfaces."},{"step":"02","title":"Foam Scrub","desc":"Agitating dry foam on fabrics."},{"step":"03","title":"Wipe & Dress","desc":"Matte finish surface dressing."}]',
 NULL, '["interior-deep-clean", "steam-wash"]',
 '1 Hour', 13, 1,
 'Interior Dry Cleaning in Jammu | The Drive Clinic',
 'Fast low-moisture interior dry cleaning in Nanak Nagar, Jammu.'),

-- 14. PREMIUM CAR WASH (Minor)
(14, 'premium-car-wash', 'PREMIUM CAR WASH', 'clean', 'car', 0, NULL,
 'Multi-step hand wash featuring pre-rinse, dual-bucket wash, wheel cleaning, and interior vacuum.',
 'CLASSIC DUAL-BUCKET HAND WASH WITH COMPLETE INTERIOR VACUUM.',
 'Our standard-setting thorough wash. High-pressure pre-wash, thick snow foam, two-bucket scratch-free hand wash, wheel face clean, and a full cabin vacuuming.',
 '["High Pressure Rinse", "Snow Foam Soak", "Two-Bucket Microfiber Hand Wash", "Interior Cabin Vacuum", "Tyre Dressing & Glass Clean"]',
 '[{"title":"Thorough Hand Wash","desc":"No automatic brushes; 100% gentle human hands and microfiber."},{"title":"Clean Inside & Out","desc":"Balanced exterior wash and interior dust extraction."}]',
 '[{"title":"Regular Car Owners","desc":"Ideal high-standard weekly or bi-weekly vehicle wash."}]',
 '[{"step":"01","title":"Pre-Wash","desc":"Rinse and foam."},{"step":"02","title":"Contact Wash","desc":"Microfiber two-bucket clean."},{"step":"03","title":"Dry & Vacuum","desc":"Blow-dry and complete vacuum."}]',
 NULL, '["ceramic-wash", "maintenance-wash"]',
 '45 Mins', 14, 1,
 'Premium Car Wash in Jammu | The Drive Clinic',
 'Thorough two-bucket hand car wash and vacuum in Nanak Nagar, Jammu.'),

-- 15. EXPRESS EXTERIOR WASH (Minor)
(15, 'express-exterior-wash', 'EXPRESS EXTERIOR WASH', 'clean', 'car', 0, NULL,
 'Fast touchless exterior foam wash and high-velocity air dry for vehicles on the move.',
 'QUICK 20-MINUTE EXTERIOR TOUCHLESS WASH AND DRY.',
 'When you are pressed for time, our Express Exterior Wash safely blasts away road dust and road grime using high-pressure filtered water, rich foaming soap, and touchless air-cannon drying.',
 '["Touchless High-Pressure Rinse", "Foam Bath & Rapid Hand Wash", "Wheel Face Clean", "Air & Microfiber Dry"]',
 '[{"title":"20-Minute Speed","desc":"Get back on the road in record time with a sparkling clean car."},{"title":"Safe Products","desc":"pH-neutral soaps that do not strip existing waxes or coatings."}]',
 '[{"title":"Busy Drivers on the Go","desc":"Quick refresh before a meeting or highway journey."}]',
 '[{"step":"01","title":"Power Rinse","desc":"High-pressure dirt removal."},{"step":"02","title":"Quick Wash","desc":"Fast foam pass and rinse."},{"step":"03","title":"Dry","desc":"Blow dry and go."}]',
 NULL, '["premium-car-wash", "maintenance-wash"]',
 '20 - 25 Mins', 15, 1,
 'Express Car Wash in Jammu | Quick Exterior Wash | The Drive Clinic',
 'Fast 20-minute express exterior car wash in Nanak Nagar, Jammu.');

-- 4. FAQS
DELETE FROM `faqs`;
INSERT INTO `faqs` (`id`, `service_id`, `question`, `answer`, `is_answered`, `sort_order`) VALUES
(1, NULL, 'Where is The Drive Clinic located in Jammu?', 'We are conveniently located in Nanak Nagar, Jammu, Jammu & Kashmir 180004. You can find our exact location on the Contact page or via Google Maps.', 1, 1),
(2, NULL, 'Do I need an appointment for a wash or detailing?', 'While we accept drive-ins based on bay availability, we strongly recommend booking an appointment online to guarantee zero waiting time and dedicated bay allocation.', 1, 2),
(3, NULL, 'What payment methods do you accept at the studio?', 'We accept UPI (Google Pay, PhonePe, Paytm), all major credit and debit cards, net banking, and cash.', 1, 3),
(4, 1, 'What is the lifespan and warranty on PPF installation?', '[ANSWER PENDING — studio to confirm warranty years, film brand and package specifications].', 0, 1),
(5, 1, 'Does the paint protection film yellow or peel over time?', 'Our high-grade aliphatic polyurethane films feature built-in UV inhibitors and high-tensile adhesive backings engineered to resist yellowing, bubbling, and edge peeling under harsh Indian climate conditions.', 1, 2),
(6, 1, 'Can minor scratches really heal on their own?', 'Yes. The top polyurethane layer has elastomeric properties. Light scratches, swirl marks, and minor abrasions flow back into a smooth state when exposed to heat (warm water, sunlight, or engine heat).', 1, 3),
(7, 2, 'How long does Ceramic Coating last on a car or bike in Jammu?', '[ANSWER PENDING — studio to confirm specific coating tiers and longevity duration].', 0, 1),
(8, 2, 'Can I wash my car normally after ceramic coating?', 'Yes! Ceramic coating makes washing significantly faster. However, avoid harsh alkaline detergents and automatic brush washes. We recommend our Ceramic Wash or Maintenance Wash for best longevity.', 1, 2),
(9, 3, 'Do you detail all types of bikes, including superbikes and scooters?', 'Yes, we detail all two-wheelers including superbikes, cruiser motorcycles, adventure tourers, and everyday commuter bikes and scooters with tailored care for each.', 1, 1),
(10, 5, 'How is a Ceramic Wash different from a regular car wash?', 'A regular wash only strips dirt (and often strips existing wax). Our Ceramic Wash uses SiO2-infused shampoos that safely lubricate grit and leave behind an active layer of ceramic gloss and hydrophobic water beading.', 1, 1),
(11, 6, 'Is steam cleaning safe for sensitive car electronics and leather?', 'Yes. We use commercial dry vapor steam with low moisture content, combined with targeted isolation of sensitive electronic components, making it 100% safe and deeply sterilizing.', 1, 1);

-- 5. AVAILABILITY RULES (7 days, 09:00 - 19:00, 60-min slots, capacity 2)
DELETE FROM `availability_rules`;
INSERT INTO `availability_rules` (`weekday`, `open_time`, `close_time`, `slot_minutes`, `capacity_per_slot`) VALUES
(0, '09:00:00', '19:00:00', 60, 2), -- Sunday
(1, '09:00:00', '19:00:00', 60, 2), -- Monday
(2, '09:00:00', '19:00:00', 60, 2), -- Tuesday
(3, '09:00:00', '19:00:00', 60, 2), -- Wednesday
(4, '09:00:00', '19:00:00', 60, 2), -- Thursday
(5, '09:00:00', '19:00:00', 60, 2), -- Friday
(6, '09:00:00', '19:00:00', 60, 2); -- Saturday

-- 6. GALLERY ITEMS
DELETE FROM `gallery_items`;
INSERT INTO `gallery_items` (`id`, `service_id`, `type`, `image`, `before_image`, `after_image`, `alt`, `vehicle_label`, `sort_order`) VALUES
(1, 1, 'before_after', NULL, '/assets/img/before-after/paint-before.jpg', '/assets/img/before-after/paint-after.jpg', 'PPF stone-chip protection before and after on front bonnet', 'Luxury Sedan • Full Front PPF', 1),
(2, 3, 'before_after', NULL, '/assets/img/bike/bike-2.jpg', '/assets/img/bike/bike-1.jpg', 'Bike detailing engine and tank restoration before and after', 'Superbike 650cc • Full Bike Detail', 2),
(3, 5, 'before_after', NULL, '/assets/img/before-after/paint-before.jpg', '/assets/img/wash/ceramic-wash.jpg', 'Ceramic wash hydrophobic gloss before and after', 'Premium SUV • Ceramic Wash', 3),
(4, 6, 'before_after', NULL, '/assets/img/before-after/interior-before.jpg', '/assets/img/before-after/interior-after.jpg', 'Steam interior cabin deep clean before and after', 'Sedan • Steam Interior Sanitization', 4),
(5, 4, 'before_after', NULL, '/assets/img/before-after/paint-before.jpg', '/assets/img/before-after/paint-after.jpg', 'Full car detailing paint correction before and after', 'Executive Sedan • Paint Correction', 5),
(6, 8, 'before_after', NULL, '/assets/img/services/engine-bay.jpg', '/assets/img/services/engine-bay.jpg', 'Engine bay steam degrease and dress before and after', '4x4 SUV • Engine Bay Detail', 6);

-- 7. CRM CUSTOMERS & VEHICLES
DELETE FROM `vehicles`;
DELETE FROM `customers`;
INSERT INTO `customers` (`id`, `name`, `phone`, `whatsapp`, `email`) VALUES
(1, 'Rahul Nair', '+91 98201 12345', '919820112345', 'rahul.nair@example.com'),
(2, 'Sneha Kapoor', '+91 98110 54321', '919811054321', 'sneha.kapoor@example.com'),
(3, 'Vikram Desai', '+91 94191 77889', '919419177889', 'vikram.desai@example.com'),
(4, 'Rohan Gupta', '+91 94192 33445', '919419233445', 'rohan.gupta@example.com'),
(5, 'Arjun Sharma', '+91 99060 99881', '919906099881', 'arjun.sharma@example.com');

INSERT INTO `vehicles` (`id`, `customer_id`, `type`, `brand`, `model`, `year`, `registration`, `color`) VALUES
(1, 1, 'car', 'Kia', 'Seltos GTX+', '2024', 'MH 02 GN 1648', 'Gravity Grey'),
(2, 2, 'car', 'Mercedes-Benz', 'GLC 300', '2025', 'MH 01 DJ 9009', 'Polar White'),
(3, 3, 'car', 'Toyota', 'Fortuner Legender', '2023', 'JK 02 CN 5544', 'Attitude Black'),
(4, 4, 'car', 'BMW', '320d M-Sport', '2024', 'JK 02 BZ 1122', 'Portimao Blue'),
(5, 5, 'car', 'Hyundai', 'Creta SX(O)', '2024', 'JK 02 AP 8899', 'Phantom Black');

-- 8. CRM APPOINTMENTS
DELETE FROM `appointments`;
INSERT INTO `appointments` (`id`, `ref`, `customer_id`, `vehicle_id`, `service_id`, `date`, `time`, `status`, `notes`, `source`) VALUES
(1, 'DRC-1001', 1, 1, 5, '2026-10-08', '10:00:00', 'in_service', 'Assigned Tech: Sameer | Bay 3', 'crm_booking'),
(2, 'DRC-1002', 2, 2, 1, '2026-10-08', '14:00:00', 'booked', 'Assigned Tech: Rohan | Bay 1 | Full PPF', 'crm_booking'),
(3, 'DRC-1003', 3, 3, 2, '2026-10-08', '15:00:00', 'booked', 'Assigned Tech: Arjun | Bay 2 | Ceramic Coating', 'crm_booking'),
(4, 'DRC-1004', 4, 4, 2, '2026-10-08', '11:30:00', 'in_service', 'Assigned Tech: Rohan | Bay 1 | Ceramic Shield', 'crm_booking'),
(5, 'DRC-1005', 5, 5, 4, '2026-10-08', '09:00:00', 'in_service', 'Assigned Tech: Arjun | Bay 2 | Signature Detailing', 'crm_booking');

-- 9. INVENTORY ITEMS (Detailing Studio Catalog)
DELETE FROM `inventory_items`;
INSERT INTO `inventory_items` (`id`, `sku`, `name`, `category`, `unit`, `current_stock`, `min_stock_alert`, `unit_cost`, `sell_price`, `supplier`, `location`, `notes`) VALUES
(1, 'PPF-STEK-DYNO', 'STEK DynoShield TPU PPF (1.52m x 15m Roll)', 'PPF & Films', 'Roll', 4.00, 2.00, 48000.00, 85000.00, 'STEK India HQ', 'Film Vault Rack A', 'Top-tier self healing 8mil hydrophobic film'),
(2, 'CER-GTECH-CSL', 'Gtechniq Crystal Serum Light (50ml)', 'Ceramic Coatings', 'Bottle', 6.00, 3.00, 6200.00, 14500.00, 'Gtechniq India', 'Bay 1 Lockup Shelf 1', '9H pro-grade ceramic coating'),
(3, 'CER-CARPRO-CQ', 'CarPro CQuartz UK 3.0 (50ml)', 'Ceramic Coatings', 'Bottle', 2.00, 3.00, 4800.00, 11000.00, 'CarPro India', 'Bay 1 Lockup Shelf 2', 'Quartz protection formula - REORDER SOON'),
(4, 'CMP-KOCH-H9', 'Koch Chemie H9.01 Heavy Cut Compound (1L)', 'Polishing & Compounds', 'Litre', 8.00, 2.00, 2400.00, 4200.00, 'Koch Chemie Delhi', 'Polish Bay Station 2', 'Coarse abrasive compound for heavy swirl removal'),
(5, 'CMP-MENZ-3800', 'Menzerna Super Finish Plus 3800 (1L)', 'Polishing & Compounds', 'Litre', 5.00, 2.00, 2100.00, 3800.00, 'Detailing Depot', 'Polish Bay Station 2', 'Ultra-fine jewel cut polish'),
(6, 'SHP-CARPRO-RST', 'CarPro Reset Intensive Car Shampoo (4L)', 'Wash & Shampoos', 'Can', 12.00, 4.00, 3100.00, 5500.00, 'CarPro India', 'Wash Bay Dispenser A', 'pH neutral coating-safe wash soap'),
(7, 'SHP-KOCH-GS', 'Koch Chemie Gentle Snow Foam (5L)', 'Wash & Shampoos', 'Can', 9.00, 3.00, 2800.00, 4900.00, 'Koch Chemie Delhi', 'Wash Bay Dispenser B', 'High foam encapsulation shampoo'),
(8, 'CHM-CARPRO-IRX', 'CarPro IronX Chemical Fallout Remover (4L)', 'Chemicals & Decon', 'Can', 3.00, 3.00, 3900.00, 6200.00, 'CarPro India', 'Decontamination Bay', 'Acid-free iron brake dust dissolver'),
(9, 'ACC-MF-EDGLS', 'Edgeless 500GSM Plush Microfibers (Pack of 10)', 'Accessories & Towels', 'Pack', 18.00, 5.00, 1200.00, 2200.00, 'Microfiber Master', 'Clean Room Storage', 'Scratch-free paint leveling towels'),
(10, 'ACC-PAD-RUPES', 'Rupes D-A Yellow Fine Foam Polishing Pad 150mm', 'Accessories & Towels', 'Piece', 14.00, 6.00, 650.00, 1100.00, 'Rupes Direct India', 'Polish Bay Pad Drawer', 'Dual action orbital finishing pad');

-- 10. INVENTORY TRANSACTIONS (Stock Movements & Decline / Wastage Audit Logs)
DELETE FROM `inventory_transactions`;
INSERT INTO `inventory_transactions` (`id`, `item_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `reference_no`, `reason`, `logged_by`, `created_at`) VALUES
(1, 1, 'IN_PURCHASE', 5.00, 48000.00, 240000.00, 'PO-2026-881', 'Stock replenishment from STEK India', 'Studio Owner', '2026-10-03 10:30:00'),
(2, 1, 'OUT_JOB_CARD', -1.00, 48000.00, 48000.00, 'JC-2026-0104', 'Consumed for Mercedes GLC Full PPF', 'Rohan (PPF Tech)', '2026-10-07 14:15:00'),
(3, 1, 'DECLINE_DAMAGED', -0.25, 48000.00, 12000.00, 'DEC-2026-001', 'Film creased & overstretched past tension limit on front bumper complex curvature', 'Rohan (PPF Tech)', '2026-10-06 16:45:00'),
(4, 3, 'DECLINE_EXPIRED', -1.00, 4800.00, 4800.00, 'DEC-2026-002', 'Bottle cap seal leaked during storage, ceramic fluid crystallized solid', 'Arjun (Master Detailer)', '2026-10-05 11:20:00'),
(5, 10, 'DECLINE_SCRAP', -2.00, 650.00, 1300.00, 'DEC-2026-003', 'Velcro backing delaminated during high-speed rotary compounding on heavy hood swirls', 'Sameer (Wash Tech)', '2026-10-07 18:00:00'),
(6, 2, 'IN_PURCHASE', 8.00, 6200.00, 49600.00, 'INV-GTECH-441', 'Monthly batch order of Crystal Serum Light', 'Studio Owner', '2026-10-04 09:15:00');

-- 11. CRM JOB CARDS
DELETE FROM `job_cards`;
INSERT INTO `job_cards` (`id`, `card_no`, `appointment_id`, `customer_id`, `vehicle_id`, `bay_no`, `assigned_tech`, `status`, `fuel_level`, `odometer`, `service_names`, `damages_json`, `consumables_json`, `technician_notes`, `total_amount`, `created_at`) VALUES
(1, 'JC-2026-0101', 4, 4, 4, 1, 'Rohan', 'IN_PROGRESS', '3/4', '18,400 km', 'Ceramic Shield 9H + Glass Treatment', '["Minor scratch on rear right quarter panel", "Swirl marks on bonnet"]', '[{"item":"Gtechniq CSL 50ml","qty":"1 Bottle"},{"item":"Edgeless MF Towel","qty":"4 Pcs"}]', 'Vehicle clayed, single-stage correction complete, applying 1st coat CSL in Bay 1.', 38000.00, '2026-10-08 10:00:00'),
(2, 'JC-2026-0102', 5, 5, 5, 2, 'Arjun', 'IN_PROGRESS', '1/2', '31,200 km', 'Signature Detailing + Leather Sanitization', '["Stone chip on front left fender", "Driver door handle scuff"]', '[{"item":"Koch Chemie H9.01","qty":"150ml"},{"item":"Rupes Yellow Pad","qty":"1 Pc"}]', 'Rotary machine cut done, interior steam extraction in progress.', 18500.00, '2026-10-08 09:30:00'),
(3, 'JC-2026-0103', 1, 1, 1, 3, 'Sameer', 'IN_PROGRESS', 'Full', '9,800 km', 'Premium Ceramic Wash + Ozone Reset', '["Vehicle in clean baseline condition"]', '[{"item":"CarPro Reset","qty":"100ml"},{"item":"Koch Chemie Snow Foam","qty":"150ml"}]', 'Foam bath and high-pressure rinse complete, drying with blowers.', 4500.00, '2026-10-08 10:15:00'),
(4, 'JC-2026-0104', 2, 2, 2, 1, 'Rohan', 'CHECKED_IN', '1/2', '4,200 km', 'Full-Body STEK DynoShield TPU PPF', '["Pristine showroom delivery condition"]', '[{"item":"STEK DynoShield PPF","qty":"1 Roll"}]', 'Surface decontamination and alcohol wipedown scheduled for 2:00 PM.', 95000.00, '2026-10-08 14:00:00');

-- 12. CRM INVOICES
DELETE FROM `invoices`;
INSERT INTO `invoices` (`id`, `invoice_no`, `job_card_id`, `customer_id`, `vehicle_id`, `subtotal`, `discount`, `gst_amount`, `grand_total`, `payment_status`, `payment_method`, `paid_amount`, `notes`, `created_at`) VALUES
(1, 'INV-2026-0840', 1, 4, 4, 35000.00, 0.00, 6300.00, 41300.00, 'PAID', 'UPI', 41300.00, 'Paid via GPay (Txn #984210481)', '2026-10-08 11:45:00'),
(2, 'INV-2026-0841', 2, 5, 5, 18500.00, 0.00, 3330.00, 21830.00, 'PAID', 'CASH', 21830.00, 'Paid cash at studio counter', '2026-10-08 12:30:00'),
(3, 'INV-2026-0842', 3, 1, 1, 4500.00, 0.00, 810.00, 5310.00, 'PAID', 'UPI', 5310.00, 'Paid via PhonePe', '2026-10-08 11:00:00'),
(4, 'INV-2026-0843', 4, 2, 2, 95000.00, 0.00, 17100.00, 112100.00, 'PARTIAL', 'UPI', 50000.00, 'Advance ₹50,000 paid. Balance ₹62,100 on delivery.', '2026-10-08 14:30:00');

-- 13. CRM WARRANTY CERTIFICATES
DELETE FROM `warranty_certificates`;
INSERT INTO `warranty_certificates` (`id`, `cert_no`, `customer_id`, `vehicle_id`, `service_slug`, `product_brand`, `roll_batch_no`, `warranty_years`, `install_date`, `expiry_date`, `next_inspection_date`, `status`, `created_at`) VALUES
(1, 'TDC-WAR-2026-001', 4, 4, 'ceramic-coating', 'Gtechniq Crystal Serum Light', 'BATCH-GTECH-2026-Q3', 5, '2026-10-08', '2031-10-08', '2027-04-08', 'ACTIVE', '2026-10-08 12:00:00'),
(2, 'TDC-WAR-2026-002', 2, 2, 'ppf', 'STEK DynoShield TPU', 'ROLL-STEK-8841-A', 10, '2026-10-08', '2036-10-08', '2027-10-08', 'ACTIVE', '2026-10-08 15:00:00');


