<?php

/**
 * Class Migration_init
 * @property CI_DB_forge $dbforge
 */
class Migration_import_univ extends CI_Migration
{
	public function up()
	{
        $this->load->database();
        $this->db->where('univ_id >',0)->delete("univ");
        $this->db->query("INSERT INTO `univ` (`univ_id`, `univ_nama`) VALUES
        (1, 'UNIVERSITAS ABDURRAB'),
        (2, 'UNIVERSITAS ABULYATAMA'),
        (3, 'UNIVERSITAS AHMAD DAHLAN'),
        (4, 'UNIVERSITAS AIRLANGGA'),
        (5, 'UNIVERSITAS AL KHAIRAAT'),
        (6, 'UNIVERSITAS ANDALAS'),
        (7, 'UNIVERSITAS BAITURRAHMAH'),
        (8, 'UNIVERSITAS BATAM'),
        (9, 'UNIVERSITAS BENGKULU'),
        (10, 'UNIVERSITAS BOSOWA'),
        (11, 'UNIVERSITAS BRAWIJAYA'),
        (12, 'UNIVERSITAS CENDERAWASIH'),
        (13, 'UNIVERSITAS CIPUTRA SURABAYA'),
        (14, 'UNIVERSITAS DIPONEGORO'),
        (15, 'UNIVERSITAS GAJAH MADA'),
        (16, 'UNIVERSITAS GUNADARMA'),
        (17, 'UNIVERSITAS HALUOLEO'),
        (18, 'UNIVERSITAS HANG TUAH'),
        (19, 'UNIVERSITAS HASANUDDIN'),
        (20, 'UNIVERSITAS HKBP NOMMENSEN'),
        (21, 'UNIVERSITAS INDONESIA'),
        (22, 'UNIVERSITAS ISLAM AL-AZHAR MATARAM'),
        (23, 'UNIVERSITAS ISLAM BANDUNG'),
        (24, 'UNIVERSITAS ISLAM INDONESIA'),
        (25, 'UNIVERSITAS ISLAM MALANG'),
        (26, 'UNIVERSITAS ISLAM NEGERI (UIN) ALAUDDIN\r\nMAKASSAR'),
        (27, 'UNIVERSITAS ISLAM NEGERI (UIN) MAULANA MALIK\r\nIBRAHIM'),
        (28, 'UNIVERSITAS ISLAM NEGERI SYARIF HIDAYATULLAH'),
        (29, 'UNIVERSITAS ISLAM SULTAN AGUNG'),
        (30, 'UNIVERSITAS ISLAM SUMATERA UTARA'),
        (31, 'UNIVERSITAS JAMBI'),
        (32, 'UNIVERSITAS JEMBER'),
        (33, 'UNIVERSITAS JENDERAL ACHMAD YANI'),
        (34, 'UNIVERSITAS JENDERAL SOEDIRMAN'),
        (35, 'UNIVERSITAS KATOLIK INDONESIA ATMA JAYA'),
        (36, 'UNIVERSITAS KHAIRUN TERNATE'),
        (37, 'UNIVERSITAS KRISTEN DUTA WACANA'),
        (38, 'UNIVERSITAS KRISTEN INDONESIA'),
        (39, 'UNIVERSITAS KRISTEN KRIDA WACANA'),
        (40, 'UNIVERSITAS KRISTEN MARANATHA'),
        (41, 'UNIVERSITAS LAMBUNG MANGKURAT'),
        (42, 'UNIVERSITAS LAMPUNG'),
        (43, 'UNIVERSITAS MALAHAYATI'),
        (44, 'UNIVERSITAS MALIKUSSALEH'),
        (45, 'UNIVERSITAS MATARAM'),
        (46, 'UNIVERSITAS METHODIST INDONESIA'),
        (47, 'UNIVERSITAS MUHAMMADIYAH JAKARTA'),
        (48, 'UNIVERSITAS MUHAMMADIYAH MAKASAR'),
        (49, 'UNIVERSITAS MUHAMMADIYAH MALANG'),
        (50, 'UNIVERSITAS MUHAMMADIYAH PALEMBANG'),
        (51, 'UNIVERSITAS MUHAMMADIYAH PURWOKERTO'),
        (52, 'UNIVERSITAS MUHAMMADIYAH SEMARANG'),
        (53, 'UNIVERSITAS MUHAMMADIYAH SUMATERA UTARA'),
        (54, 'UNIVERSITAS MUHAMMADIYAH SURABAYA'),
        (55, 'UNIVERSITAS MUHAMMADIYAH SURAKARTA'),
        (56, 'UNIVERSITAS MUHAMMADIYAH YOGYAKARTA'),
        (57, 'UNIVERSITAS MULAWARMAN'),
        (58, 'UNIVERSITAS MUSLIM INDONESIA'),
        (59, 'UNIVERSITAS NEGERI PAPUA'),
        (60, 'UNIVERSITAS NU SURABAYA'),
        (61, 'UNIVERSITAS NUSA CENDANA'),
        (62, 'UNIVERSITAS PADJADJARAN'),
        (63, 'UNIVERSITAS PALANGKARAYA'),
        (64, 'UNIVERSITAS PATTIMURA'),
        (65, 'UNIVERSITAS PELITA HARAPAN'),
        (66, 'UNIVERSITAS PEMBANGUNAN NASIONAL VETERAN\r\nJAKARTA'),
        (67, 'UNIVERSITAS PRIMA INDONESIA'),
        (68, 'UNIVERSITAS Prof.Dr. HAMKA'),
        (69, 'UNIVERSITAS RIAU'),
        (70, 'UNIVERSITAS SAM RATULANGI'),
        (71, 'UNIVERSITAS SEBELAS MARET'),
        (72, 'UNIVERSITAS SRIWIJAYA'),
        (73, 'UNIVERSITAS SUMATERA UTARA'),
        (74, 'UNIVERSITAS SURABAYA'),
        (75, 'UNIVERSITAS SWADAYA GUNUNG DJATI'),
        (76, 'UNIVERSITAS SYIAH KUALA'),
        (77, 'UNIVERSITAS TADULAKO'),
        (78, 'UNIVERSITAS TANJUNGPURA'),
        (79, 'UNIVERSITAS TARUMANEGARA'),
        (80, 'UNIVERSITAS TRISAKTI'),
        (81, 'UNIVERSITAS UDAYANA'),
        (82, 'UNIVERSITAS WAHID HASYIM SEMARANG'),
        (83, 'UNIVERSITAS WARMADEWA'),
        (84, 'UNIVERSITAS WIDYA MANDALA'),
        (85, 'UNIVERSITAS WIJAYA KUSUMA SURABAYA'),
        (86, 'UNIVERSITAS YARSI'),
        (9999, 'OTHER');");
      
	}

	public function down()
	{
        $this->load->database();
        $this->db->delete("univ");
	}

}