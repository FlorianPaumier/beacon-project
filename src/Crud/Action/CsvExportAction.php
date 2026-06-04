<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Action;

use Symfony\Component\HttpFoundation\Response;

final readonly class CsvExportAction
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * @param iterable<object> $entities
     * @param string[]         $fields
     * @param string[]|null    $headers
     */
    public function export(iterable $entities, array $fields, string $filename, ?array $headers = null): Response
    {
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($entities, $fields, $headers) {
            $handle = fopen('php://output', 'w');

            if ($headers !== null) {
                fputcsv($handle, $headers);
            } else {
                fputcsv($handle, array_map('ucfirst', $fields));
            }

            foreach ($entities as $entity) {
                $row = [];

                foreach ($fields as $field) {
                    $value = $this->extractValue($entity, $field);
                    $row[] = $value;
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    private function extractValue(object $entity, string $field): string
    {
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $current = $entity;

            foreach ($parts as $part) {
                $current = $this->getPropertyValue($current, $part);

                if ($current === null) {
                    return '';
                }

                if (!\is_object($current)) {
                    return $this->sanitizeCsvValue((string) $current);
                }
            }

            return $this->sanitizeCsvValue($this->objectToString($current));
        }

        $value = $this->getPropertyValue($entity, $field);

        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (\is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (\is_object($value)) {
            return $this->sanitizeCsvValue($this->objectToString($value));
        }

        return $this->sanitizeCsvValue((string) $value);
    }

    private function sanitizeCsvValue(string $value): string
    {
        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'".$value;
        }

        return $value;
    }

    private function getPropertyValue(object $entity, string $field): mixed
    {
        $getter = 'get'.ucfirst($field);
        $isser = 'is'.ucfirst($field);
        $hasser = 'has'.ucfirst($field);

        if (method_exists($entity, $getter)) {
            // @phpstan-ignore method.dynamicName
            return $entity->{$getter}();
        }

        if (method_exists($entity, $isser)) {
            // @phpstan-ignore method.dynamicName
            return $entity->{$isser}();
        }

        if (method_exists($entity, $hasser)) {
            // @phpstan-ignore method.dynamicName
            return $entity->{$hasser}();
        }

        if (method_exists($entity, $field)) {
            // @phpstan-ignore method.dynamicName
            return $entity->{$field}();
        }

        $reflection = new \ReflectionClass($entity);

        if ($reflection->hasProperty($field)) {
            $prop = $reflection->getProperty($field);

            return $prop->getValue($entity);
        }

        return '';
    }

    private function objectToString(object $object): string
    {
        if (method_exists($object, '__toString')) {
            return (string) $object;
        }

        if (method_exists($object, 'getId')) {
            return (string) $object->getId();
        }

        return spl_object_hash($object);
    }
}
