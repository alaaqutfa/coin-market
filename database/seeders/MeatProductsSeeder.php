<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MeatProduct;

class MeatProductsSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['barcode' => '49',  'name' => 'KETEF GHAMA B 3ADMO', 'selling_price' => 8.33],
            ['barcode' => '33',  'name' => 'ZOR MAKROUM', 'selling_price' => 7.5],
            ['barcode' => '43',  'name' => 'LYE GHANAM', 'selling_price' => 16],
            ['barcode' => '46',  'name' => 'ME3LE2 GHANAM', 'selling_price' => 9.5],
            ['barcode' => '45',  'name' => 'BAYD 3EJEL', 'selling_price' => 7.5],
            ['barcode' => '44',  'name' => 'KETEF GHANAM BALA 3ADEM', 'selling_price' => 8.5],
            ['barcode' => '41',  'name' => '3AS3OUS 3EJEL', 'selling_price' => 9.5],
            ['barcode' => '42',  'name' => 'NAYE MCHAKAL', 'selling_price' => 23],
            ['barcode' => '108', 'name' => 'JWANEH MTABAL', 'selling_price' => 1.9],
            ['barcode' => '109', 'name' => 'NUGGETS', 'selling_price' => 7.9],
            ['barcode' => '110', 'name' => 'CORDON BLUE DJEJ', 'selling_price' => 6.5],
            ['barcode' => '111', 'name' => 'ESCALOPE DJEJ/CRISPY', 'selling_price' => 6.5],
            ['barcode' => '101', 'name' => 'DABBOUS', 'selling_price' => 2.8],
            ['barcode' => '99',  'name' => 'FKHAD', 'selling_price' => 1.35],
            ['barcode' => '100', 'name' => 'JWENEH', 'selling_price' => 1.8],
            ['barcode' => '102', 'name' => 'SODER', 'selling_price' => 5],
            ['barcode' => '103', 'name' => 'FARROUJ', 'selling_price' => 3.32],
            ['barcode' => '63',  'name' => 'FKHAD MSAHAB', 'selling_price' => 3.9],
            ['barcode' => '64',  'name' => '3ASAB 3EJEL', 'selling_price' => 4.9],
            ['barcode' => '60',  'name' => 'LSEN GHANAM MAKROUM', 'selling_price' => 17],
            ['barcode' => '61',  'name' => 'BIFTEK / SHAWE BALADI', 'selling_price' => 10.5],
            ['barcode' => '62',  'name' => 'NKHA3 GHANAM', 'selling_price' => 18],
            ['barcode' => '54',  'name' => 'CORDON BLUE LAHME', 'selling_price' => 6.5],
            ['barcode' => '59',  'name' => 'LSEN BA2AR MAKROUM', 'selling_price' => 6.15],
            ['barcode' => '57',  'name' => 'T7AL BA2AR', 'selling_price' => 10.5],
            ['barcode' => '55',  'name' => 'WRAK DJEJ', 'selling_price' => 1],
            ['barcode' => '58',  'name' => 'KOTLETTE HALABE', 'selling_price' => 9.9],
            ['barcode' => '52',  'name' => 'LSEN GHANAM KEMEL', 'selling_price' => 14],
            ['barcode' => '50',  'name' => 'CHICKEN BURGER', 'selling_price' => 1.5],
            ['barcode' => '48',  'name' => 'ROSTO HALABE', 'selling_price' => 9.9],
            ['barcode' => '53',  'name' => 'SAWDA BA2AR', 'selling_price' => 10.5],
            ['barcode' => '47',  'name' => 'MKANEK W SEJOUK', 'selling_price' => 11],
            ['barcode' => '56',  'name' => 'MAWZET BRAZILE', 'selling_price' => 6.9],
            ['barcode' => '51',  'name' => 'LSEN BA2AR KEMEL', 'selling_price' => 5.9],
            ['barcode' => '38',  'name' => 'BA2AR OFFRE', 'selling_price' => 8.33],
            ['barcode' => '39',  'name' => 'JUGO GHANAM', 'selling_price' => 13.33],
            ['barcode' => '40',  'name' => 'FILET GHANAM NAY', 'selling_price' => 19.5],
            ['barcode' => '107', 'name' => 'FAHITA', 'selling_price' => 6],
            ['barcode' => '105', 'name' => 'TAWOUK ABAYAD', 'selling_price' => 3.61],
            ['barcode' => '106', 'name' => 'TAWOUK AHMAR', 'selling_price' => 3.61],
            ['barcode' => '104', 'name' => 'SAWDA', 'selling_price' => 1.95],
            ['barcode' => '36',  'name' => 'KAFTA', 'selling_price' => 5.5],
            ['barcode' => '37',  'name' => 'LAHME GHANAM', 'selling_price' => 14],
            ['barcode' => '32',  'name' => 'LAHME BA2AR', 'selling_price' => 9.5],
            ['barcode' => '34',  'name' => 'FILET 3EJEL BALADI', 'selling_price' => 12.5],
            ['barcode' => '35',  'name' => 'BURGER', 'selling_price' => 2.77],
        ];

        foreach ($products as $p) {
            MeatProduct::create([
                'barcode' => $p['barcode'],
                'name' => $p['name'],
                'description' => '',
                'current_stock' => 0,
                'cost_price' => $p['selling_price'],
                'selling_price' => $p['selling_price'],
                'waste_percentage' => 0,
                'is_active' => 1,
            ]);
        }

        $this->command->info('Added ' . count($products));
    }
}
