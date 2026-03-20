<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
           'Building Construction Labourers, Other',
            'Helper Mason',
            'Bricklayerer, Construction',
            'Builders, Traditional Materials, Other',
            'Carpenters and Joiners Operatives, Other',
            'Concrete Placers, Concrete Finishers and Related Workers, Other',
            'Painters and Related Workers, Other',
            'Stone Splitters, Cutters and Carvers, Other',
            'Plumbers and Pipe Fitters, Other',
            'Helper Electrician',
            'Lineman, Light and Power/Distribution Lineman',
            'Tractor Driver, Construction',
            'Helper Bar Bender and Fixer',
            'Fitter, Construction/Reinforcement Fitter',
            'Floor Layers and Tile Setters, Other',
            'Plasterers, Other',
            'Maintenance Assistant/Helper',
            'Roofers, Other',
            'Assistant Shuttering Carpenter',
            'Furniture Maker, Wood',
            'Mechanic, Refrigeration and Air Conditioning',
            'Concrete Mixer Operator',
            'Picture Framer/Glaziers, Other',
            'White Washer',
            'Insulation Workers, Other',
            'Scrapper Loader Operator/Loader Operator',
            'Acoustical Insulator',
            'Wood Polisher',
            'Bulldozer Operator',
            'Power Shovel Operator/Excavator Operator',
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(['name' => $skill]);
        }
    }
}
