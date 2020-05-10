<?php

declare(strict_types=1);

namespace App\DataProvider;

/**
 * Class MainDataProvider
 * @package App\DataProvider
 */
class MainDataProvider
{
    public const SESSION_ERROR_KEY = "__errors";

    public const INDEX_PATH         = "page_index";
    public const DELETE_AUTHOR_PATH = "page_author_delete";
    public const DELETE_BOOK_PATH   = "page_book_delete";
    public const LIST_AUTHOR_PATH   = "page_authors_list";
    public const LIST_BOOK_PATH     = "page_books_list";

    /**
     * @param string $path
     * @return string|null
     */
    public static function getTargetName(string $path): ?string
    {
        $data = [
            self::DELETE_AUTHOR_PATH => self::LIST_AUTHOR_PATH,
            self::DELETE_BOOK_PATH   => self::LIST_BOOK_PATH,
        ];

        return array_key_exists($path, $data) ? $data[$path] : self::INDEX_PATH;
    }
}
