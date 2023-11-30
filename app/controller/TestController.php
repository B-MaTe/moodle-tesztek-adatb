<?php

namespace controller;
use Database;
use DateTime;
use enum\SqlValueType;
use Exception;
use model\Test;
use util\pageable\Page;
use util\pageable\Pageable;

require_once 'app/controller/Controller.php';
require_once 'app/model/Test.php';

class TestController extends Controller
{

    public function index(): void
    {

        $data = $this->getActivePageable(Pageable::builder()->withPageSize(5)->withTotalRecords($this->countActiveTests())->build());
        require_once 'app/view/tests.php';
    }

    public function getActivePageable(Pageable $pageable): Page {
        $page = new Page([], $pageable->getPage(), $pageable->getPageSize(), $pageable->getTotalRecords());

        $testPageSql = 'select * from tests where active = true ORDER BY id LIMIT ?, ?';

        $page->setItems(self::selectModels($testPageSql, false, [SqlValueType::INT->value, SqlValueType::INT->value], [$pageable->getOffset(), $pageable->getPageSize()]));
        return $page;
    }

    private function countActiveTests(): int {
        $sql = 'select count(*) from tests where active = true';

        return Database::query($sql, [], [])->fetch_row()[0];
    }

    private static function selectModels(string $query, bool $single, array $types = [], array $params = []): array|Test|null {
        $tests = [];
        $result = Database::query($query, $types, $params);
        $test = $result->fetch_assoc();

        do {
            if ($test != null) {
                if (isset($test['created_at'])) {
                    try {
                        $test['created_at'] = new DateTime($test['created_at']);
                    } catch (Exception $ignored) {
                        $test['created_at'] = null;
                    }
                }

                $tests[] = new Test(...$test);
            }
        } while ($test = $result->fetch_assoc() && !$single);

        return $single ? ($tests[0] ?? null) : $tests;
    }
}