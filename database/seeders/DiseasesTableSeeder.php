<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiseasesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared('INSERT INTO `diseases` (`id`, `name`, `category`, `description`, `baseline_prevalence`) VALUES
(1, \'Myocardial Infarction\', \'Cardiac\', \'A blockage of blood flow to the heart muscle causing damage.\', 0),
(2, \'Chronic Obstructive Pulmonary Disease (COPD)\', \'Pulmonary\', \'A group of lung diseases that block airflow and make it difficult to breathe.\', 0),
(3, \'Acute Pancreatitis\', \'Pancreatic\', \'A sudden inflammation of the pancreas that can be life-threatening.\', 0),
(4, \'Angina Pectoris\', \'Cardiac\', \'A condition marked by severe chest pain due to reduced blood flow to the heart.\', 0),
(5, \'Hypertension\', \'Cardiac\', \'A condition in which the force of the blood against artery walls is too high.\', 0),
(6, \'Atrial Fibrillation\', \'Cardiac\', \'A heart rhythm disorder causing irregular and often rapid heartbeat.\', 0),
(7, \'Congestive Heart Failure\', \'Cardiac\', \'A condition where the heart is unable to pump blood effectively.\', 0),
(8, \'Pericarditis\', \'Cardiac\', \'Inflammation of the pericardium, the sac surrounding the heart.\', 0),
(9, \'Mitral Valve Prolapse\', \'Cardiac\', \'A condition where the valve between the heart\\'s left chambers doesn\\'t close properly.\', 0),
(10, \'Asthma\', \'Pulmonary\', \'A chronic respiratory condition characterized by airway inflammation and bronchospasm.\', 0),
(11, \'Pneumonia\', \'Pulmonary\', \'An infection that inflames the air sacs in one or both lungs.\', 0),
(12, \'Pulmonary Embolism\', \'Pulmonary\', \'A blockage in one of the pulmonary arteries in the lungs.\', 0),
(13, \'Tuberculosis\', \'Pulmonary\', \'A serious infectious disease that mainly affects the lungs.\', 0),
(14, \'Bronchitis\', \'Pulmonary\', \'Inflammation of the bronchial tubes, often following a cold or respiratory infection.\', 0),
(15, \'Interstitial Lung Disease\', \'Pulmonary\', \'A group of lung conditions that cause scarring of lung tissue.\', 0),
(16, \'Chronic Pancreatitis\', \'Pancreatic\', \'A long-standing inflammation of the pancreas that alters its normal structure and functions.\', 0),
(17, \'Pancreatic Cancer\', \'Pancreatic\', \'A type of cancer that begins in the tissues of the pancreas.\', 0),
(18, \'Gastroesophageal Reflux Disease (GERD)\', \'Pancreatic\', \'A digestive disorder where stomach acid irritates the food pipe lining.\', 0),
(19, \'Gallstone Disease (Cholelithiasis)\', \'Pancreatic\', \'Formation of stones in the gallbladder that can block bile flow.\', 0),
(20, \'Peptic Ulcer Disease\', \'Pancreatic\', \'Sores that develop in the stomach lining or the upper part of the small intestine.\', 0),
(21, \'Appendicitis\', \'Gastrointestinal\', \'Inflammation of appendix causing right lower abdominal pain, nausea, vomiting, and fever;');
    }
}
