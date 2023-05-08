<?php


namespace Service {
    require_once __DIR__ . '/../Entity/Siswa.php';
    use Entity\Siswa;
    use Repository\SiswaRepository;

    interface SiswaService
    {
        function showSiswa(): array;
        public function addSiswa(string $siswa, int $nis, string $kelas, int $id_spp): void;
        function removeSiswa(int $number): bool;
    }

    class SiswaServiceImpl implements SiswaService
    {
        private SiswaRepository $siswaRepository;

        public function __construct(SiswaRepository $siswaRepository)
        {
            $this->siswaRepository = $siswaRepository;
        }

        public function showSiswa(): array
        {
            $siswaList = $this->siswaRepository->findAll();

            return $siswaList;
        }

        public function addSiswa(string $siswa, int $nis, string $kelas, int $id_spp): void
        {
            // Retrieve the golongan from the database based on the id_spp
            $sql = "SELECT golongan FROM spp WHERE id_spp = ?";
            $statement = $this->siswaRepository->connection->prepare($sql);
            $statement->execute([$id_spp]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);

            if (!$result) {
                throw new \Exception("SPP with id $id_spp not found");
            }

            // Create a new Siswa object with the retrieved data
            $newSiswa = new Siswa($siswa);
            $newSiswa->setNis($nis);
            $newSiswa->setKelas($kelas);
            $newSiswa->setIdSpp($id_spp);
            $newSiswa->setGolongan($result['golongan']);

            $this->siswaRepository->add($newSiswa);
        }


        public function removeSiswa(int $number): bool
        {
            return $this->siswaRepository->remove($number);
        }
    }
}