<?php

namespace Database\Seeders;

use App\Models\Cake;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Cardápio de demonstração: 50 bolos e tortas em 8 categorias.
 * Formato: [nome, resumo, preço em centavos, rendimento, cor, destaque].
 */
class CatalogSeeder extends Seeder
{
    public const CATALOG = [
        'Bolos caseiros' => [
            ['Bolo de Cenoura com Brigadeiro', 'Massa fofinha de cenoura com cobertura generosa de brigadeiro cremoso.', 6990, '14 fatias', '#E07A2E', true],
            ['Bolo de Fubá Cremoso', 'Receita de vó: fubá, queijo e uma camada cremosa que se forma ao assar.', 5490, '12 fatias', '#E3B23C', false],
            ['Bolo de Laranja', 'Massa leve de laranja natural com calda cítrica.', 5290, '12 fatias', '#F2994A', false],
            ['Bolo de Milho Verde', 'Milho fresco batido, textura úmida e aroma de festa junina.', 5490, '12 fatias', '#E8C547', false],
            ['Bolo de Mandioca', 'Mandioca ralada, coco e leite condensado, bem cremoso por dentro.', 5990, '12 fatias', '#EAD9A8', false],
            ['Bolo de Aipim com Coco', 'Aipim cozido com coco fresco e um toque de erva-doce.', 5990, '12 fatias', '#F1E4C3', false],
            ['Bolo de Banana Caramelizada', 'Banana-prata em calda de caramelo sobre massa de canela.', 6290, '12 fatias', '#D9A441', false],
            ['Bolo de Iogurte com Limão', 'Massa leve de iogurte natural com glacê de limão-siciliano.', 5790, '12 fatias', '#C9D94A', false],
            ['Bolo Formigueiro', 'Massa amanteigada com granulado de chocolate e cobertura de brigadeiro.', 5990, '14 fatias', '#9C6B4E', false],
            ['Bolo de Coco Gelado', 'Massa branca molhadinha com leite de coco, servida gelada no papel.', 6490, '16 pedaços', '#F3EEE3', true],
        ],
        'Chocolate' => [
            ['Brigadeirão Trufado', 'Massa de chocolate 50% cacau com recheio trufado e brigadeiro de colher.', 12990, '20 fatias', '#6B3E26', true],
            ['Bolo Prestígio', 'Chocolate com recheio cremoso de coco e cobertura de ganache.', 11990, '20 fatias', '#7A4A2A', false],
            ['Bolo Nega Maluca', 'O clássico de chocolate com cobertura brilhante e úmida.', 5990, '14 fatias', '#4E2A1E', false],
            ['Chocolate com Morango', 'Massa de chocolate, brigadeiro branco e morangos frescos.', 13990, '20 fatias', '#8E3B46', true],
            ['Bolo Sensação', 'Chocolate com mousse de morango e cobertura de ganache meio amargo.', 13490, '20 fatias', '#A03A4A', false],
            ['Bolo Ninho com Nutella', 'Massa branca, creme de leite Ninho e camadas de creme de avelã.', 14990, '20 fatias', '#C9A47E', true],
            ['Bolo Trufado de Maracujá', 'Chocolate meio amargo com mousse de maracujá azedinho.', 13490, '20 fatias', '#D4A017', false],
            ['Bolo de Chocolate Branco e Pistache', 'Massa de baunilha, ganache de chocolate branco e pistache torrado.', 16990, '20 fatias', '#9DB46C', false],
        ],
        'Festa' => [
            ['Floresta Negra', 'Pão de ló de chocolate, chantilly, cerejas e raspas de chocolate meio amargo.', 15990, '25 fatias', '#5B2C2C', true],
            ['Red Velvet', 'Massa aveludada com recheio e cobertura de cream cheese.', 16990, '25 fatias', '#B0273B', true],
            ['Naked Cake de Frutas Vermelhas', 'Camadas de pão de ló com creme de baunilha e frutas vermelhas frescas.', 18990, '30 fatias', '#C2416B', true],
            ['Bolo Dois Amores', 'Recheio de brigadeiro preto e branco intercalados, coberto com chantininho.', 15490, '25 fatias', '#7B5A4A', false],
            ['Bolo Abacaxi com Coco', 'Pão de ló branco, creme de coco e abacaxi cozido em calda.', 14490, '25 fatias', '#F2D16B', false],
            ['Bolo de Nozes', 'Pão de ló com nozes, doce de leite e cobertura de fios de ovos.', 17990, '25 fatias', '#A0754E', false],
            ['Bolo Ouro Branco', 'Chocolate, creme de amendoim e pedaços de bombom crocante.', 15990, '25 fatias', '#D9B56C', false],
            ['Bolo Ninho com Morango', 'O queridinho das festas: creme de Ninho e morangos frescos.', 15490, '25 fatias', '#E9B8C6', true],
        ],
        'Frutas' => [
            ['Bolo de Maracujá', 'Massa de maracujá com mousse leve e calda da fruta.', 6990, '14 fatias', '#E8B830', false],
            ['Bolo de Limão com Merengue', 'Creme de limão e merengue maçaricado sobre massa amanteigada.', 7490, '14 fatias', '#DDE36B', false],
            ['Bolo de Morango com Chantilly', 'Pão de ló leve, chantilly fresco e morangos inteiros.', 12990, '20 fatias', '#E5577B', true],
            ['Bolo de Ameixa', 'Massa úmida com ameixas-pretas e cobertura de doce de ameixa.', 6990, '14 fatias', '#6E3B5E', false],
            ['Bolo de Goiabada com Queijo', 'O romeu e julieta em forma de bolo, com goiabada cascão.', 6490, '14 fatias', '#C0392B', false],
            ['Bolo de Maçã com Canela', 'Maçãs fatiadas, canela e farofinha crocante por cima.', 6290, '12 fatias', '#B5653D', false],
        ],
        'Tortas doces' => [
            ['Torta Holandesa', 'Base de biscoito, creme holandês e cobertura de ganache.', 9990, '12 fatias', '#C8A27A', true],
            ['Torta de Limão', 'Massa crocante, creme de limão e merengue suíço maçaricado.', 8490, '10 fatias', '#E6E07A', true],
            ['Torta Banoffee', 'Doce de leite, banana, chantilly e raspas de chocolate.', 8990, '10 fatias', '#C69C5D', false],
            ['Torta de Morango', 'Creme de confeiteiro e morangos com geleia brilhante.', 9490, '10 fatias', '#D94F6B', false],
            ['Torta Alemã', 'Creme de manteiga, bolacha champanhe e cobertura de chocolate.', 9990, '12 fatias', '#8B5E3C', false],
            ['Torta de Maçã', 'Massa amanteigada com recheio de maçã e canela, coberta com treliça.', 8490, '10 fatias', '#C9853E', false],
            ['Torta Mousse de Chocolate', 'Base de cookie com mousse aerado de chocolate 70%.', 9490, '12 fatias', '#5A3324', false],
            ['Torta de Nozes', 'Recheio caramelado com nozes sobre massa sablée.', 10990, '12 fatias', '#9A6A43', false],
            ['Cheesecake de Frutas Vermelhas', 'Cheesecake assado com calda de frutas vermelhas.', 10990, '12 fatias', '#B23A5A', true],
            ['Torta de Pistache', 'Creme de pistache, base crocante e pistaches inteiros por cima.', 12990, '12 fatias', '#93B26B', false],
        ],
        'Tortas salgadas' => [
            ['Torta de Frango com Catupiry', 'Massa podre caseira, frango desfiado e requeijão cremoso.', 7990, '10 fatias', '#E4B363', true],
            ['Quiche Lorraine', 'Bacon, queijo gruyère e creme de ovos assado na massa amanteigada.', 7490, '8 fatias', '#E9C46A', false],
            ['Torta de Palmito', 'Recheio cremoso de palmito com azeitonas e cheiro-verde.', 7990, '10 fatias', '#C8B86B', false],
            ['Quiche de Alho-Poró', 'Alho-poró refogado na manteiga com queijo parmesão.', 7290, '8 fatias', '#A9C27A', false],
            ['Empadão de Camarão', 'Camarão ao molho de tomate e leite de coco, massa que desmancha.', 11990, '10 fatias', '#E8845C', false],
        ],
        'Sem lactose' => [
            ['Bolo de Banana com Canela', 'Feito com leite vegetal, sem lactose, com banana caramelizada.', 5990, '12 fatias', '#C9A227', false],
            ['Chocolate com Leite de Coco', 'Massa úmida de cacau feita com leite de coco.', 7490, '14 fatias', '#8B5A3C', true],
            ['Bolo de Cenoura Vegano', 'Sem ovos e sem leite, com cobertura de chocolate 70%.', 6990, '12 fatias', '#E07A2E', false],
        ],
    ];

    public function run(): void
    {
        $position = 0;
        foreach (self::CATALOG as $categoryName => $cakes) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'position' => $position],
            );
            $position++;

            foreach ($cakes as [$name, $summary, $price, $serves, $color, $featured]) {
                Cake::updateOrCreate(['slug' => Str::slug($name)], [
                    'category_id' => $category->id,
                    'name' => $name,
                    'summary' => $summary,
                    'description' => $summary.' Preparado artesanalmente, sob encomenda, com ingredientes selecionados. '
                        .'Retirada na loja a partir de 2 dias após o pedido.',
                    'price_cents' => $price,
                    'serves' => $serves,
                    'color' => $color,
                    'is_featured' => $featured,
                    'is_active' => true,
                ]);
            }
        }
    }
}
