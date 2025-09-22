<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\ApiKey;
use App\Models\Garden;
use App\Models\Plant;
use App\Models\Plot;
use App\Models\Variety;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class PlotController extends Controller
{
    public function overview()
    {
        $all_plots = Plot::with('garden')->withCount('plants')->orderBy('id', 'desc')->get();

        $apikeys = ApiKey::first()->token_key;
        // dd($apikeys);
        return view('map.overview', compact('all_plots', 'apikeys'));
    }
    public function findByCode(Request $request)
    {
        $code = trim((string)$request->query('code', ''));
        if ($code === '') return response()->json(null);

        $norm = function ($s) {
            $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            $s = strtolower(preg_replace('/[^a-z0-9]/', '', $s));
            return $s;
        };

        $q = $norm($code);

        $plant = Plant::query()
            ->select('id', 'plantCode', 'plotID', 'lat', 'lng', 'year', 'statusTree', 'varietyID')
            ->whereRaw("LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(plantCode,' ',''),'-',''),'.',''),'/',''),'_',''),'''','')) LIKE ?", ["%$q%"])
            ->first();

        if (!$plant) return response()->json(null);
        // Nếu cần tên giống
        $varietyName = optional(Variety::find($plant->varietyID))->name;
        return response()->json([
            'id' => $plant->id,
            'plantCode' => $plant->plantCode,
            'plotID' => $plant->plotID,
            'lat' => (float)$plant->lat,
            'lng' => (float)$plant->lng,
            'year' => $plant->year,
            'statusTree' => $plant->statusTree,
            'varietyName' => $varietyName,
        ]);
    }
    public function index(Request $request)
    {
        $apikeys = ApiKey::first()->token_key;
        $gardens = Garden::all();
        $all_plots = Plot::with('garden')->withCount('plants')->orderBy('id', 'desc')->get();
        // dd($apikeys);
        if ($request->ajax()) {
            return DataTables::of($all_plots)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('plotName', function ($row) {
                    return $row->plotName;
                })
                ->editColumn('plotName', function ($row) {
                    return $row->plotName;
                })
                ->addColumn('plants_count', function ($row) {
                    return $row->plants_count ?? '';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                       <button class="btn btn-info btn-sm view-map" data-id_plot="' . $row->id . '">
                            <i class="fas fa-map-marker-alt"></i>
                        </button>
                            <a href="/edit-plots/' . $row->id . '" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
                                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->plotName ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/plots/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'plotName', 'gardenID', 'plotArea', 'plants_count', 'status', 'action'])
                ->make(true);
        }
        return view('plots.all_plots', compact('gardens', 'all_plots', 'apikeys'));
    }
    public function add()
    {
        // $gardens = Garden::all();
        $totalPlants = Plot::withCount('plants')->get()->sum('plants_count');

        return view('plots.add_plots', compact('totalPlants'));
    }
    private function makePlantPointsFromMapJs(?string $mapJs, int $count): array
    {
        if (!$mapJs || $count <= 0) return [];
        $rings = $this->extractRingsFromMap($mapJs);
        if (!$rings || empty($rings)) return [];
        $outer = $this->pickLargestOuterRing($rings);
        if (!$outer || count($outer) < 3) return [];
        [$minLng, $minLat, $maxLng, $maxLat] = $this->bboxFromRing($outer);
        $n = (int)ceil(sqrt($count));
        $dx = ($maxLng - $minLng) / max($n + 1, 1);
        $dy = ($maxLat - $minLat) / max($n + 1, 1);
        $pts = [];
        for ($i = 1; $i <= $n && count($pts) < $count; $i++) {
            for ($j = 1; $j <= $n && count($pts) < $count; $j++) {
                $lng = $minLng + $dx * $i;
                $lat = $minLat + $dy * $j;
                if ($this->pointInPolygon($lng, $lat, $outer)) {
                    $pts[] = [$lat, $lng];
                }
            }
        }
        $tries = 0;
        while (count($pts) < $count && $tries < 10000) {
            $lng = $minLng + lcg_value() * ($maxLng - $minLng);
            $lat = $minLat + lcg_value() * ($maxLat - $minLat);
            if ($this->pointInPolygon($lng, $lat, $outer)) {
                $pts[] = [$lat, $lng];
            }
            $tries++;
        }
        if (count($pts) < $count) {
            [$cLng, $cLat] = $this->centroidOfRing($outer);
            while (count($pts) < $count) {
                $pts[] = [$cLat, $cLng];
            }
        }
        return array_slice($pts, 0, $count);
    }
    private function extractRingsFromMap(string $mapJs): ?array
    {
        $obj = json_decode($mapJs, true);
        if (!$obj) return null;
        if (isset($obj['rings']) && is_array($obj['rings'])) {
            return $obj['rings']; // [[ [lng,lat], ... ], [hole]...]
        }
        if (isset($obj['type'])) {
            if ($obj['type'] === 'Feature' && isset($obj['geometry'])) {
                $geom = $obj['geometry'];
                return $this->extractRingsFromGeometry($geom);
            }
            if ($obj['type'] === 'FeatureCollection' && isset($obj['features']) && is_array($obj['features'])) {
                foreach ($obj['features'] as $f) {
                    if (isset($f['geometry'])) {
                        $rings = $this->extractRingsFromGeometry($f['geometry']);
                        if ($rings) return $rings;
                    }
                }
            }
            return $this->extractRingsFromGeometry($obj);
        }
        return null;
    }
    private function extractRingsFromGeometry(array $geom): ?array
    {
        if (!isset($geom['type'])) return null;
        $type = $geom['type'];
        if ($type === 'Polygon' && isset($geom['coordinates'])) {
            return $geom['coordinates'];
        }
        if ($type === 'MultiPolygon' && isset($geom['coordinates'])) {
            $best = null;
            $bestArea = -INF;
            foreach ($geom['coordinates'] as $poly) {
                if (!is_array($poly) || empty($poly)) continue;
                $outer = $poly[0];
                $area  = $this->polygonArea($outer);
                if ($area > $bestArea) {
                    $bestArea = $area;
                    $best = $poly;
                }
            }
            return $best;
        }
        return null;
    }
    private function pickLargestOuterRing(array $rings): ?array
    {
        $best = null;
        $bestArea = -INF;
        foreach ($rings as $ring) {
            if (!is_array($ring) || count($ring) < 3) continue;
            $area = $this->polygonArea($ring);
            if ($area > $bestArea) {
                $bestArea = $area;
                $best = $ring;
            }
        }
        return $best;
    }
    private function polygonArea(array $ring): float
    {
        $n = count($ring);
        $a = 0.0;
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $x1 = (float)$ring[$j][0];
            $y1 = (float)$ring[$j][1];
            $x2 = (float)$ring[$i][0];
            $y2 = (float)$ring[$i][1];
            $a += ($x1 * $y2 - $x2 * $y1);
        }
        return abs($a) * 0.5;
    }
    private function bboxFromRing(array $ring): array
    {
        $minLng = $minLat = INF;
        $maxLng = $maxLat = -INF;
        foreach ($ring as $p) {
            $lng = (float)$p[0];
            $lat = (float)$p[1];
            if ($lng < $minLng) $minLng = $lng;
            if ($lng > $maxLng) $maxLng = $lng;
            if ($lat < $minLat) $minLat = $lat;
            if ($lat > $maxLat) $maxLat = $lat;
        }
        return [$minLng, $minLat, $maxLng, $maxLat];
    }
    private function pointInPolygon(float $lng, float $lat, array $ring): bool
    {
        $inside = false;
        $n = count($ring);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = (float)$ring[$i][0];
            $yi = (float)$ring[$i][1];
            $xj = (float)$ring[$j][0];
            $yj = (float)$ring[$j][1];
            $intersect = (($yi > $lat) !== ($yj > $lat)) &&
                ($lng < ($xj - $xi) * ($lat - $yi) / ((($yj - $yi) ?: 1e-12)) + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
    }
    private function centroidOfRing(array $ring): array
    {
        $n = count($ring);
        $a = 0.0;
        $cx = 0.0;
        $cy = 0.0;
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $x1 = (float)$ring[$j][0];
            $y1 = (float)$ring[$j][1];
            $x2 = (float)$ring[$i][0];
            $y2 = (float)$ring[$i][1];
            $cross = ($x1 * $y2 - $x2 * $y1);
            $a += $cross;
            $cx += ($x1 + $x2) * $cross;
            $cy += ($y1 + $y2) * $cross;
        }
        $a *= 0.5;
        if (abs($a) < 1e-12) {
            $sumX = $sumY = 0.0;
            foreach ($ring as $p) {
                $sumX += (float)$p[0];
                $sumY += (float)$p[1];
            }
            $m = max(count($ring), 1);
            return [$sumX / $m, $sumY / $m];
        }
        return [$cx / (6.0 * $a), $cy / (6.0 * $a)];
    }
    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'plotName'      => 'required|string|max:255',
                'mapJs'         => 'required',
                'fid'           => 'nullable|integer',
                'idmap'         => 'nullable|string|max:255',
                'year'          => 'nullable|integer|min:1900|max:' . date('Y'),
                'chi_tieu'      => 'nullable|string|max:255',
                'plotArea'      => 'nullable|numeric|min:0',
                'tapping_y'     => 'nullable|integer|min:0',
                'repl_time'     => 'nullable',
                'find'          => 'nullable|string|max:255',
                'webmap'        => 'nullable|string|max:255',
                'gwf'           => 'nullable|string|max:255',
                'xa'            => 'nullable|string|max:255',
                'huyen'         => 'nullable|string|max:255',
                'nguon_goc_lo'  => 'nullable|string|max:255',
                'nguon_goc_dat' => 'nullable|string|max:255',
                'hang_dat'      => 'nullable|string|max:255',
                'hien_trang'    => 'nullable|string|max:255',
                'layer'         => 'nullable|string|max:255',
                'x'             => 'nullable|string|max:255',
                'y'             => 'nullable|string|max:255',
                'chu_thich'     => 'nullable|string|max:1000',
                'varietyID'     => 'nullable|exists:varieties,id',
                'varietyName'   => 'nullable|string|max:255',
                'origin'        => 'nullable|string|max:255',
                'desc'          => 'nullable|string|max:1000',
                'plantCount'    => 'nullable|integer|min:0|max:50000',
                'plant_year'    => 'nullable|integer|min:1900|max:' . date('Y'),
                'statusTree'    => 'nullable|string|max:100',
                'lat'           => 'nullable|numeric',
                'lng'           => 'nullable|numeric',
            ], [
                'plotName.required' => 'Tên lô là bắt buộc.',
                'mapJs.required'    => 'Bản đồ là bắt buộc.',
            ]);

            $currentYear = (int) date('Y');
            if ($request->filled('year') && (int)$request->year > $currentYear) {
                return back()->with('error', 'Năm trồng không được lớn hơn năm hiện tại.')->withInput();
            }

            // Tạo plotCode: year.plotName.find (bỏ dấu . thừa)
            $plotCode = ($request->year ?? '') . '.' . $request->plotName . '.' . ($request->find ?? '');
            $plotCode = trim($plotCode, '.');

            // Chống trùng theo plotName / plotCode
            $dup = Plot::where('plotName', $request->plotName)
                ->orWhere('plotCode', $plotCode)
                ->first();
            if ($dup) {
                return back()->with(['error' => 'Lô này đã tồn tại!'])->withInput();
            }

            // Inputs cây
            $plantCount = (int) $request->input('plantCount', 0);
            $plantYear  = $request->input('plant_year', $request->input('year'));
            $statusTree = $request->input('statusTree');

            // Nếu tạo cây thì buộc có giống
            if (
                $plantCount > 0
                && !$request->filled('chi_tieu')
                && !$request->filled('varietyID')
                && !$request->filled('varietyName')
            ) {
                return back()->with('error', 'Vui lòng nhập "Chỉ tiêu" (tên giống) hoặc chọn/nhập giống trước khi tạo cây.')
                    ->withInput();
            }

            DB::transaction(function () use ($request, $validated, $plotCode, $plantCount, $plantYear, $statusTree) {

                // === Resolve GIỐNG ===
                $varietyId = null;
                if ($request->filled('varietyID')) {
                    $varietyId = (int)$request->input('varietyID');
                } else {
                    $rawName = (string) ($request->input('chi_tieu', $request->input('varietyName', '')));
                    $vName   = \Illuminate\Support\Str::of($rawName)->trim()->squish()->value();
                    if ($vName !== '') {
                        $variety = Variety::firstOrCreate(
                            ['varietyName' => $vName],
                            [
                                'origin' => $request->input('origin'),
                                'desc'   => $request->input('desc'),
                                'status' => 'Hoạt động',
                            ]
                        );
                        $varietyId = $variety->id;
                    }
                }
                if ($plantCount > 0 && !$varietyId) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'chi_tieu' => 'Vui lòng nhập "Chỉ tiêu" (tên giống) hoặc chọn giống trước khi tạo cây.',
                    ]);
                }

                // === Tạo Plot ===
                $plotData = $validated;
                $plotData['plotCode']   = $plotCode;
                $plotData['status']     = $plotData['status'] ?? 'Hoạt động';
                $plotData['plantCount'] = $plantCount;
                unset(
                    $plotData['varietyID'],
                    $plotData['varietyName'],
                    $plotData['origin'],
                    $plotData['desc'],
                    $plotData['plantCount'],
                    $plotData['plant_year'],
                    $plotData['statusTree'],
                    $plotData['lat'],
                    $plotData['lng']
                );
                /** @var Plot $plot */
                $plot = Plot::create($plotData);

                // === Sinh cây từ mapJs (nếu có) ===
                if ($plantCount > 0) {
                    // Lấy toạ độ từng cây từ mapJs (ưu tiên); nếu không đủ thì vẫn tạo với null
                    $points = $this->makePlantPointsFromMapJs($request->input('mapJs'), $plantCount);

                    $now  = now();
                    $rows = [];
                    for ($i = 1; $i <= $plantCount; $i++) {
                        // Lấy toạ độ theo index (0-based)
                        $latLng = $points[$i - 1] ?? [null, null]; // [lat, lng]
                        $rows[] = [
                            'plantCode'  => $plot->plotCode . '.' . str_pad($i, 3, '0', STR_PAD_LEFT),
                            'plotID'     => $plot->id,
                            'varietyID'  => $varietyId,
                            'RF_id'      => null,
                            'year'       => $plantYear,
                            'status'     => 'Hoạt động',
                            'statusTree' => $statusTree ?: 'Tốt',
                            'lat'        => $latLng[0],
                            'lng'        => $latLng[1],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    // Kiểm tra trùng plantCode trước khi insert (khuyến nghị vẫn tạo unique index ở DB)
                    $newCodes = array_column($rows, 'plantCode');
                    $dups = Plant::whereIn('plantCode', $newCodes)->pluck('plantCode')->all();
                    if (!empty($dups)) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'plantCount' => 'Mã cây đã tồn tại: ' . implode(', ', $dups),
                        ]);
                    }

                    Plant::insert($rows);
                }

                // === Lịch sử ===
                $chiTieu = \Illuminate\Support\Str::of((string)$request->input('chi_tieu', $request->input('varietyName', '')))->trim()->squish()->value();
                ActionHistory::create([
                    'user_id'     => \Illuminate\Support\Facades\Auth::id(),
                    'action_type' => 'Tạo',
                    'model_type'  => 'Khu Vực Trồng',
                    'details'     => 'Tạo lô: ' . $plotCode .
                        ' | Năm: ' . ($request->year ?? '-') .
                        ' | Find: ' . ($request->find ?? '-') .
                        ' | Variety: ' . ($chiTieu ?: ($request->varietyID ?? 'null')) .
                        ' | Plants: ' . $plantCount,
                ]);
            });

            return redirect()->route('plots.index')->with(
                'message',
                $plantCount > 0
                    ? "Tạo lô thành công, đã gán toạ độ cây từ bản đồ."
                    : "Tạo lô thành công."
            );
        } catch (\Illuminate\Validation\ValidationException $ve) {
            throw $ve;
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        // $gardens = Garden::all();
        $plots = Plot::find($id);
        $totalPlants = $plots->plants()->count();
        return view('plots.edit_plots', compact('plots', 'totalPlants'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'plotCode' => 'required',
            'plotName' => 'required',
            'mapJs' => 'required',
            'status' => 'nullable',
            'fid' => 'nullable|integer',
            'idmap' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'chi_tieu' => 'nullable|string|max:255',
            'plotArea' => 'nullable|numeric|min:0',
            'tapping_y' => 'nullable|integer|min:0',
            'repl_time' => 'nullable',
            'find' => 'nullable|string|max:255',
            'webmap' => 'nullable|string|max:255',
            'gwf' => 'nullable|string|max:255',
            'xa' => 'nullable|string|max:255',
            'huyen' => 'nullable|string|max:255',
            'nguon_goc_lo' => 'nullable|string|max:255',
            'nguon_goc_dat' => 'nullable|string|max:255',
            'hang_dat' => 'nullable|string|max:255',
            'hien_trang' => 'nullable|string|max:255',
            'layer' => 'nullable|string|max:255',
            'x' => 'nullable|string|max:255',
            'y' => 'nullable|string|max:255',
            'chu_thich' => 'nullable|string|max:1000',
        ], [
            'plotCode.required' => 'Mã cây trồng là bắt buộc.',
            'plotName.required' => 'Tên lô là bắt buộc.',
            'plotArea.required' => 'Diện tích lô là bắt buộc.',
            'year.required' => 'Năm là bắt buộc.',
            'mapJs.required' => 'Bản đồ là bắt buộc.',
            'fid.integer' => 'Fid phải là một số nguyên.',
            'idmap.max' => 'ID Map không được vượt quá 255 ký tự.',
            'year.integer' => 'Năm trồng phải là một số nguyên.',
            'year.min' => 'Năm trồng phải từ 1900 trở lên.',
            'year.max' => 'Năm trồng không được vượt quá năm hiện tại (' . date('Y') . ').',
            'chi_tieu.max' => 'Chỉ tiêu không được vượt quá 255 ký tự.',
            'plotArea.numeric' => 'Diện tích phải là một số.',
            'plotArea.min' => 'Diện tích không được nhỏ hơn 0.',
            'tapping_y.integer' => 'Tapping Y phải là một số nguyên.',
            'tapping_y.min' => 'Tapping Y không được nhỏ hơn 0.',
            'find.max' => 'Find không được vượt quá 255 ký tự.',
            'webmap.max' => 'Webmap không được vượt quá 255 ký tự.',
            'gwf.max' => 'GWF không được vượt quá 255 ký tự.',
            'xa.max' => 'Xã không được vượt quá 255 ký tự.',
            'huyen.max' => 'Huyện không được vượt quá 255 ký tự.',
            'nguon_goc_lo.max' => 'Nguồn gốc lô không được vượt quá 255 ký tự.',
            'nguon_goc_dat.max' => 'Nguồn gốc đất không được vượt quá 255 ký tự.',
            'hang_dat.max' => 'Hạng đất không được vượt quá 255 ký tự.',
            'hien_trang.max' => 'Hiện trạng không được vượt quá 255 ký tự.',
            'layer.max' => 'Layer không được vượt quá 255 ký tự.',
            'x.max' => 'X không được vượt quá 255 ký tự.',
            'y.max' => 'Y không được vượt quá 255 ký tự.',
            'chu_thich.max' => 'Chú thích không được vượt quá 1000 ký tự.',
        ]);

        $plot = Plot::find($id);

        if (!$plot) {
            return redirect()->back()->with('error', 'Lô không tồn tại');
        }
        $plotCode = $request->year . '.' . $request->plotName . '.' . $request->find;
        $plot->update([
            'plotCode' => $plotCode,
            'fid' => $request->fid,
            'idmap' => $request->idmap,
            'nha_sx' => $request->nha_sx,
            'plotName' => $request->plotName,
            'year' => $request->year,
            'chi_tieu' => $request->chi_tieu,
            'plotArea' => $request->plotArea,
            'tapping_y' => $request->tapping_y,
            'repl_time' => $request->repl_time,
            'find' => $request->find,
            'webmap' => $request->webmap,
            'gwf' => $request->gwf,
            'xa' => $request->xa,
            'huyen' => $request->huyen,
            'nguon_goc_lo' => $request->nguon_goc_lo,
            'nguon_goc_dat' => $request->nguon_goc_dat,
            'hang_dat' => $request->hang_dat,
            'hien_trang' => $request->hien_trang,
            'layer' => $request->layer,
            'chu_thich' => $request->chu_thich,
            'x' => $request->x,
            'y' => $request->y,
            'mapJs' => $request->mapJs,
        ]);
        $oldPlot = $plot->getOriginal();

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Cập Nhật',
            'model_type' => 'Khu Vực Trồng',
            'details' => "Cập nhật khu trồng: 
            Cũ - Mã lô: {$oldPlot['plotCode']} | Năm trồng: {$oldPlot['year']} | Find: {$oldPlot['find']} 
            Mới - Mã lô: $plotCode | Năm trồng: {$request->year} | Find: {$request->find}"
        ]);
        return redirect()->route('plots.index')->with('message', 'Cập nhật lô thành công.');
    }

    public function destroy($id)
    {
        $plots = Plot::find($id);
        $plots->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $plots = Plot::whereIn('id', $request->ids)->get();

        foreach ($plots as $p) {
            $p->status = ($p->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $p->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $plotsToDelete = Plot::whereIn('id', $request->ids)->get();

        Plot::whereIn('id', $request->ids)->delete();

        foreach ($plotsToDelete as $p) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Plot',  // Model "Plot"
                'details' => "Đã xóa lô: " . $p->plotName . " với mã: " . $p->plotCode,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các lô được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $plot = Plot::find($request->id);
        if ($plot) {
            $plot->status = $plot->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $plot->save();
            return response()->json(['success' => true, 'status' => $plot->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
