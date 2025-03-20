<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function fetchData(Request $request): JsonResponse
    {
        try {
            $response = Http::get('https://bit.ly/48ejMhW');
            $data = $response->json();
            
            if ($data['RC'] !== 200) {
                return response()->json(['message' => $data['RCM']], 400);
            }
            
            $rows = explode("\n", $data['DATA']);
            if (count($rows) < 2) {
                return response()->json(['message' => 'Data format invalid or empty'], 400);
            }

            $headers = array_map('trim', explode('|', strtoupper($rows[0])));
            $idxYMD = array_search('YMD', $headers);
            $idxNAMA = array_search('NAMA', $headers);
            $idxNIM = array_search('NIM', $headers);
            
            if ($idxYMD === false || $idxNAMA === false || $idxNIM === false) {
                return response()->json(['message' => 'Invalid data format, missing required fields'], 400);
            }
            
            $result = [];
            foreach (array_slice($rows, 1) as $row) {
                $cols = explode('|', $row);
                $result[] = [
                    'YMD' => $cols[$idxYMD] ?? '',
                    'NAMA' => $cols[$idxNAMA] ?? '',
                    'NIM' => $cols[$idxNIM] ?? '',
                ];
            }
            
            $nama = $request->query('nama');
            $ymd = $request->query('ymd');
            $nim = $request->query('nim');
            $size = $request->query('size', 10);
            $page = $request->query('page', 1);
            
            if ($nama) {
                $result = array_filter($result, fn($item) => stripos($item['NAMA'], $nama) !== false);
            }
            if ($ymd) {
                $result = array_filter($result, fn($item) => $item['YMD'] === $ymd);
            }
            if ($nim) {
                $result = array_filter($result, fn($item) => $item['NIM'] === $nim);
            }
            
            $totalRecords = count($result);
            $totalPages = ceil($totalRecords / $size);
            $offset = ($page - 1) * $size;
            $paginatedData = array_slice($result, $offset, $size);
            
            return response()->json([
                'status' => 'success',
                'totalRecords' => $totalRecords,
                'data' => array_values($paginatedData),
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'pageSize' => count($paginatedData),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data', 'error' => $e->getMessage()], 500);
        }
    }
}