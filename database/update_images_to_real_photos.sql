-- =====================================================================
-- Run this in phpMyAdmin if you already imported an older schema.sql
-- and don't want to re-import from scratch. It repoints every mess's
-- cover_image to real, credited food photographs from Wikimedia
-- Commons (see README.md "Photo credits" for attribution / licenses).
-- These load over the internet, same as the map already does.
-- =====================================================================
USE mealconnect;

UPDATE mess SET cover_image = CASE id
  WHEN 1  THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Traditional_North_Indian_Thali.jpg?width=600'
  WHEN 2  THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Tiffin_box.jpg?width=600'
  WHEN 3  THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/A_thali_with_daal_roti_bhindi_ki_sabzi_and_mango_pickle.jpg?width=600'
  WHEN 4  THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Chicken_curry.jpg?width=600'
  WHEN 5  THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Popula_dabbas.JPG?width=600'
  WHEN 6  THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Veg_Punjabi_Thaali.jpg?width=600'
  WHEN 7  THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Chicken_Tikka_Masala_Curry.png?width=600'
  WHEN 8  THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Awadhi_Vegetable_Biryani.jpg?width=600'
  WHEN 9  THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Indian_Veg_Thali.JPG?width=600'
  WHEN 10 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Palak_paneer_with_rice.jpg?width=600'
  WHEN 11 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/The_Gujarati_Thali.jpg?width=600'
  WHEN 12 THEN "https://commons.wikimedia.org/wiki/Special:FilePath/Bengali_Mutton_Curry.JPG?width=600"
  WHEN 13 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Marwadi_Gujarati_Thali.jpg?width=600'
  WHEN 14 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Roti-Sabzi-Raita.JPG?width=600'
  WHEN 15 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Egg_curry.jpg?width=600'
  WHEN 16 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/A_Typical_Indian_Thali.jpg?width=600'
  WHEN 17 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Paneer_Butter_Masala.jpg?width=600'
  WHEN 18 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Gujarati_thali.jpg?width=600'
  WHEN 19 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Dabbawala.jpg?width=600'
  WHEN 20 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/A_Typical_Kerala_Spread.jpg?width=600'
  WHEN 21 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/A_typical_south_Indian_lunch_plate.jpg?width=600'
  WHEN 22 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Fish_Curry_Kerala.jpg?width=600'
  WHEN 23 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/North_Indian_thali.jpg?width=600'
  WHEN 24 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Idli_Sambar.JPG?width=600'
  WHEN 25 THEN "https://commons.wikimedia.org/wiki/Special:FilePath/TandooriPaneer.JPG?width=600"
  WHEN 26 THEN 'https://commons.wikimedia.org/wiki/Special:FilePath/Assamese_non-veg_thali.jpg?width=600'
  ELSE cover_image
END;
