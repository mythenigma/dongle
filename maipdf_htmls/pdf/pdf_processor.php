<?php 
// PDF处理专用脚本 - 通过fetch异步调用
ini_set("display_errors", true);
ini_set("html_errors", false);

// 设置中文编码支持
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');

// 设置长时间执行和大内存
set_time_limit(300); // 5分钟
ini_set('memory_limit', '512M');

// 设置响应头
header('Content-Type: text/plain; charset=UTF-8');

// 计算当前日期路径
$year = date("Y");
$month = date("m");
$week = date("d");

$fileplaceSHOW = "/".$year."/".$month."/".$week."/";
$fileplace = "yes/".$year."/".$month."/".$week."/";
$picplace = "yes/".$year."/".$month."/".$week."/preview/";

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Error: Only POST method allowed\n";
    exit;
}

// 获取POST数据
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['filename'])) {
    http_response_code(400);
    echo "Error: Missing filename parameter\n";
    exit;
}

$filename = $input['filename'];

// 处理中文文件名编码问题
echo "Raw filename: " . $filename . "\n";
echo "Filename encoding: " . mb_detect_encoding($filename, ['UTF-8', 'GBK', 'GB2312'], true) . "\n";

// 如果文件名不是UTF-8编码，尝试转换
if (mb_detect_encoding($filename, 'UTF-8', true) === false) {
    // 尝试从GBK转换到UTF-8
    $filename_gbk = mb_convert_encoding($filename, 'UTF-8', 'GBK');
    echo "Converted filename (GBK->UTF-8): " . $filename_gbk . "\n";
    
    // 尝试从GB2312转换到UTF-8
    $filename_gb2312 = mb_convert_encoding($filename, 'UTF-8', 'GB2312');
    echo "Converted filename (GB2312->UTF-8): " . $filename_gb2312 . "\n";
    
    // 检查哪个转换后的文件名对应的文件存在
    $test_file_gbk = $fileplace . $filename_gbk;
    $test_file_gb2312 = $fileplace . $filename_gb2312;
    $test_file_original = $fileplace . $filename;
    
    echo "Testing file existence:\n";
    echo "  Original: " . $test_file_original . " - " . (file_exists($test_file_original) ? "EXISTS" : "NOT FOUND") . "\n";
    echo "  GBK converted: " . $test_file_gbk . " - " . (file_exists($test_file_gbk) ? "EXISTS" : "NOT FOUND") . "\n";
    echo "  GB2312 converted: " . $test_file_gb2312 . " - " . (file_exists($test_file_gb2312) ? "EXISTS" : "NOT FOUND") . "\n";
    
    // 使用存在的文件名
    if (file_exists($test_file_gbk)) {
        $filename = $filename_gbk;
        echo "Using GBK converted filename\n";
    } elseif (file_exists($test_file_gb2312)) {
        $filename = $filename_gb2312;
        echo "Using GB2312 converted filename\n";
    } else {
        echo "Using original filename\n";
    }
} else {
    echo "Filename is already UTF-8 encoded\n";
}

// 验证文件是否存在
$source_file = $fileplace . $filename;
// if (!file_exists($source_file)) {
//     http_response_code(404);
//     echo "Error: Source file not found - " . $source_file . "\n";
//     exit;
// }

// 只处理PDF文件
// $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
// if ($extension !== 'pdf') {
//     echo "Skipped: Not a PDF file, processing skipped\n";
//     exit;
// }

try {
    // 直接执行qpdf线性化
    $output_file = $picplace . $filename;
    
    // 添加版本标识
    echo "=== PDF_PROCESSOR_V3 - CHINESE FILENAME SUPPORT ===\n";
    echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
    echo "Final filename: " . $filename . "\n";
    echo "Filename hex: " . bin2hex($filename) . "\n";
    echo "Source file: " . $source_file . "\n";
    echo "Output file: " . $output_file . "\n";
    echo "Source file exists: " . (file_exists($source_file) ? "YES" : "NO") . "\n";
    if (file_exists($source_file)) {
        echo "Source file size: " . filesize($source_file) . " bytes\n";
    }
    echo "=========================================\n";
    
    $qpdf_cmd = "qpdf " . escapeshellarg($source_file) . " --linearize --object-streams=generate " . escapeshellarg($output_file) . " 2>&1";
    
    echo "Command: " . $qpdf_cmd . "\n";
    
    exec($qpdf_cmd, $output, $return_code);
    
    echo "Return code: " . $return_code . "\n";
    echo "Raw qpdf output:\n";
    if (empty($output)) {
        echo "(no output from qpdf)\n";
    } else {
        foreach ($output as $line) {
            echo "  " . $line . "\n";
        }
    }
    
    echo "=========================================\n";
    if ($return_code === 0 && file_exists($output_file)) {
        echo "SUCCESS: Linearized file created at " . $output_file . "\n";
        echo "Output file size: " . filesize($output_file) . " bytes\n";
    } else {
        echo "FAILED: No output file created or command failed\n";
        if (!file_exists($output_file)) {
            echo "Output file does not exist\n";
        }
    }
    echo "=========================================\n";
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
