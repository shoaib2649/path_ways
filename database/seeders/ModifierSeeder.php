<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modifier;

class ModifierSeeder extends Seeder
{
    public function run(): void
    {
        $modifiers = [
            ["value" => "90837", "label" => "90837 Psychotherapy"],
            ["value" => "00001", "label" => "00001 Late cancel - free"],
            ["value" => "00002", "label" => "00002 Late cancel - charge"],
            ["value" => "00003", "label" => "00003 Late Cancel/No show - \$100"],
            ["value" => "00004", "label" => "00004 Late Cancel/No Show - \$30"],
            ["value" => "22222", "label" => "22222 Practicum Student Intake Session"],
            ["value" => "33333", "label" => "33333 Practicum Student therapy session"],
            ["value" => "55555", "label" => "55555 BrainWorks Cognitive Assessment"],
            ["value" => "90791", "label" => "90791 Psychiatric Diagnostic Evaluation"],
            ["value" => "90832", "label" => "90832 Psychotherapy, 30 min"],
            ["value" => "90834", "label" => "90834 Psychotherapy, 45 min"],
            ["value" => "90839", "label" => "90839 Psychotherapy for Crisis, 60 min"],
            ["value" => "90846", "label" => "90846 Family Psychotherapy without patient present"],
            ["value" => "90847", "label" => "90847 Family psychotherapy, conjoint psychotherapy wi..."],
            ["value" => "90853", "label" => "90853 Group Therapy"],
            ["value" => "96127", "label" => "96127 Brief emotional/behavioral assessment (eg, depr..."],
            ["value" => "96130", "label" => "96130 Psych testing eval, 1st hour"],
            ["value" => "96131", "label" => "96131 Psych testing eval, each addl hour"],
            ["value" => "96132", "label" => "96132 Neuropsych testing, first hour"],
            ["value" => "96133", "label" => "96133 Neuropsych testing, each addl hour"],
            ["value" => "96136", "label" => "96136 Psych test admin & scoring, first 30 min"],
            ["value" => "96137", "label" => "96137 Psych test admin, each addl 30 minutes"],
            ["value" => "96138", "label" => "96138 Psych/neuro test by technician, 1st 30 minutes"],
            ["value" => "96139", "label" => "96139 Psych/neuro test by technician, each addl 30 mi..."],
            ["value" => "96146", "label" => "96146 Single automated assessment instrument via elec..."],
            ["value" => "99070", "label" => "99070 Materials and supplies used in non-surgical visits"],
            ["value" => "+90785", "label" => "+90785 HW Interactive Complexity Add-On"],
            ["value" => "+90840", "label" => "Headway +90840 HW Additional Crisis Therapy, 30 min"],
            ["value" => "90791", "label" => "90791 HW Intake"],
            ["value" => "90832", "label" => "90832 HW 30 min therapy"],
            ["value" => "90834", "label" => "90834 HW 45 min therapy"],
            ["value" => "90837", "label" => "90837 HW Psychotherapy"],
            ["value" => "90839", "label" => "90839 HW Crisis"],
        ];

        foreach ($modifiers as $item) {
            Modifier::create([
                'cpt_code' => is_numeric($item['value']) ? $item['value'] : null,
                'description' => $item['label'],
                'fees' => null,
            ]);
        }
    }
}
