<?php

namespace App\Gateways;

use App\Enums\OracleProcedure;
use App\Enums\OracleSchema;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use PDO;

class BercutOracleProcedureGateway
{
    public Connection $connection;

    public function __construct()
    {
        $this->connection = DB::connection('BERCUTDB');
    }

    public function gift2GB(string $i_msisdn): array
    {
        // 1. Получаем название схемы и имя процедуры из Enum
        $schemaName = OracleSchema::SMASTER->value;
        $procedureName = OracleProcedure::Gift2Gb->value;

        // 2. Получаем сырой PDO объект для работы с привязкой OUT-параметров
        $pdo = $this->connection->getPdo();

        // 3. Инициализируем OUT-параметры
        $o_res = 0;
        $o_err = '';

        // 4. Подготавливаем PL/SQL блок
        $stmt = $pdo->prepare("BEGIN $schemaName.$procedureName(:i_msisdn, :o_res, :o_err); END;");

        // 5. Привязываем IN-параметр
        $stmt->bindParam(':i_msisdn', $i_msisdn);

        // 6. Привязываем OUT-параметры
        $stmt->bindParam(':o_res', $o_res, PDO::PARAM_INT);
        $stmt->bindParam(':o_err', $o_err, PDO::PARAM_STR, 4000);

        // 7. Выполняем процедуру
        $stmt->execute();

        return [
            'msisdn' => $i_msisdn,
            'resultCode' => $o_res,
            'errorMessage' => $o_err,
        ];
    }
}
