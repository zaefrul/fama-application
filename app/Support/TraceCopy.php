<?php

namespace App\Support;

class TraceCopy
{
    /**
     * @return array<string, string>
     */
    public static function shared(string $lang): array
    {
        $bundle = [
            'bm' => [
                'inactiveTitle' => 'QR Belum Diaktifkan',
                'inactiveBody' => 'QR ini telah dijana tetapi belum diaktifkan selepas kelulusan FAMA.',
                'invalid' => 'Kod QR tidak sah.',
                'product' => 'Maklumat Keluaran Pertanian',
                'export' => 'Maklumat Eksport',
                'certs' => 'Sijil',
                'certsNote' => 'Contoh sijil yang biasa dipamer usahawan. Ditanda CONTOH — bukan pengesahan rasmi rekod ini.',
                'sample' => 'CONTOH',
                'nutrition' => 'Nutrisi',
                'productName' => 'Nama produk',
                'origin' => 'Negara asal',
                'exporter' => 'Usahawan',
                'company' => 'Nama Syarikat',
                'farm' => 'Ladang',
                'lot' => 'No. Lot',
                'farmLocation' => 'Lokasi ladang',
                'destination' => 'Destinasi',
                'scans' => 'Imbasan',
                'scanBody' => 'QR ini telah dibuka oleh pengunjung awam.',
                'malaysia' => 'Malaysia',
                'pamphletTitle' => 'Jejak Keluaran Pertanian',
                'confidence' => 'Maklumat ini dikeluarkan untuk rujukan pembeli.',
                'officialNo' => 'No. Jejak',
                'originShort' => 'Hasil Malaysia',
                'stamp' => 'Kod QR rasmi',
                'fruitType' => 'Jenis Keluaran Pertanian',
                'agencies' => 'Hubungi Kami',
                'agenciesNote' => 'Portal rasmi FAMA. Tiada nombor telefon dipaparkan.',
                'agencyMalaysia' => 'Kerajaan Malaysia',
                'agencyFama' => 'FAMA',
                'contactLink' => 'www.fama.gov.my',
                'profileTitle' => 'Profil Keluaran Pertanian',
                'exporterAddress' => 'Alamat Syarikat',
                'chain' => 'Jejak pecahan',
                'thisChunk' => 'Kuantiti kod ini',
                'currentStop' => 'Kod ini',
                'sold' => 'Dijual',
                'soldToPublic' => 'Pengguna',
            ],
            'en' => [
                'inactiveTitle' => 'QR Not Activated',
                'inactiveBody' => 'This QR has been generated but is not active until FAMA approval.',
                'invalid' => 'Invalid QR code.',
                'product' => 'Agricultural produce information',
                'export' => 'Export information',
                'certs' => 'Certificates',
                'certsNote' => 'Sample certificates entrepreneurs typically display. Marked CONTOH — not official verification of this record.',
                'sample' => 'SAMPLE',
                'nutrition' => 'Nutrition',
                'productName' => 'Product name',
                'origin' => 'Country of origin',
                'exporter' => 'Entrepreneur',
                'company' => 'Company name',
                'farm' => 'Farm',
                'lot' => 'Lot no.',
                'farmLocation' => 'Farm location',
                'destination' => 'Destination',
                'scans' => 'Scans',
                'scanBody' => 'This QR has been opened by public visitors.',
                'malaysia' => 'Malaysia',
                'pamphletTitle' => 'Agricultural produce trace',
                'confidence' => 'This information is issued for buyer reference.',
                'officialNo' => 'Trace no.',
                'originShort' => 'Malaysian produce',
                'stamp' => 'Official QR code',
                'fruitType' => 'Agricultural produce type',
                'agencies' => 'Contact us',
                'agenciesNote' => 'Official FAMA portal. No telephone number is shown.',
                'agencyMalaysia' => 'Government of Malaysia',
                'agencyFama' => 'FAMA',
                'contactLink' => 'www.fama.gov.my',
                'profileTitle' => 'Agricultural produce profile',
                'exporterAddress' => 'Company address',
                'chain' => 'Breakdown trace',
                'thisChunk' => 'Quantity on this code',
                'currentStop' => 'This code',
                'sold' => 'Sold',
                'soldToPublic' => 'Consumer',
            ],
            'zh' => [
                'inactiveTitle' => 'QR 尚未激活',
                'inactiveBody' => '此二维码已生成，但尚未在 FAMA 批准后激活。',
                'invalid' => '无效的二维码。',
                'product' => '农产品信息',
                'export' => '出口信息',
                'certs' => '证书',
                'certsNote' => '业者常展示的证书样例。标有 CONTOH — 非本记录的正式核验。',
                'sample' => '样例',
                'nutrition' => '营养',
                'productName' => '产品名称',
                'origin' => '原产国',
                'exporter' => '业者',
                'company' => '公司名称',
                'farm' => '农场',
                'lot' => '地段编号',
                'farmLocation' => '农场位置',
                'destination' => '目的地',
                'scans' => '扫码次数',
                'scanBody' => '此二维码已被公众打开。',
                'malaysia' => '马来西亚',
                'pamphletTitle' => '农产品溯源',
                'confidence' => '本信息供买方参考。',
                'officialNo' => '溯源编号',
                'originShort' => '马来西亚农产品',
                'stamp' => '官方二维码',
                'fruitType' => '农产品种类',
                'agencies' => '联系我们',
                'agenciesNote' => 'FAMA 官方网站。不显示电话号码。',
                'agencyMalaysia' => '马来西亚政府',
                'agencyFama' => 'FAMA',
                'contactLink' => 'www.fama.gov.my',
                'profileTitle' => '农产品档案',
                'exporterAddress' => '公司地址',
                'chain' => '拆分追溯',
                'thisChunk' => '此码数量',
                'currentStop' => '此码',
                'sold' => '已售',
                'soldToPublic' => '消费者',
            ],
        ];

        return $bundle[$lang] ?? $bundle['bm'];
    }

    /**
     * @return array<string, string>
     */
    public static function forPage(string $lang, bool $livestock): array
    {
        $copy = self::shared($lang);
        if (! $livestock) {
            return $copy;
        }

        $overrides = [
            'bm' => [
                'product' => 'Maklumat Ternakan',
                'fruitType' => 'Jenis ternakan',
                'breed' => 'Baka',
                'headCount' => 'Bilangan',
                'headUnit' => 'ekor',
                'weight' => 'Berat',
                'vetCertificate' => 'No. sijil veterinar',
                'farm' => 'Premis',
                'farmLocation' => 'Lokasi premis',
                'slaughterDate' => 'Tarikh sembelih',
                'abattoir' => 'Rumah sembelih',
                'pamphletTitle' => 'Jejak Haiwan Ternakan',
                'profileTitle' => 'Profil Ternakan',
            ],
            'en' => [
                'product' => 'Livestock information',
                'fruitType' => 'Livestock type',
                'breed' => 'Breed',
                'headCount' => 'Head count',
                'headUnit' => 'head',
                'weight' => 'Weight',
                'vetCertificate' => 'Veterinary certificate no.',
                'farm' => 'Premises',
                'farmLocation' => 'Premises location',
                'slaughterDate' => 'Slaughter date',
                'abattoir' => 'Abattoir',
                'pamphletTitle' => 'Livestock trace',
                'profileTitle' => 'Livestock profile',
            ],
            'zh' => [
                'product' => '牲畜信息',
                'fruitType' => '牲畜种类',
                'breed' => '品种',
                'headCount' => '数量',
                'headUnit' => '头',
                'weight' => '重量',
                'vetCertificate' => '兽医证书编号',
                'farm' => '场所',
                'farmLocation' => '场所位置',
                'slaughterDate' => '屠宰日期',
                'abattoir' => '屠宰场',
                'pamphletTitle' => '牲畜溯源',
                'profileTitle' => '牲畜档案',
            ],
        ];

        return array_merge($copy, $overrides[$lang] ?? $overrides['bm']);
    }
}
