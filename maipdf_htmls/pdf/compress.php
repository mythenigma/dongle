<?php 
// ========================================
// PDF压缩专用脚本 - 处理preview目录中的线性化文件
// ========================================
// 
// 作用：
// 1. 接收从maipdf.php传来的文件名
// 2. 优先处理preview目录中的线性化文件
// 3. 使用Ghostscript直接压缩文件（覆盖原文件）
// 4. 不检测文件大小，直接执行压缩命令
// 5. 输出详细的处理日志
//
// 工作流程：
// maipdf.php → pdf_processor.php (线性化) → compress.php (压缩)
// ========================================

ini_set("display_errors", true);
ini_set("html_errors", false);

// 设置中文编码支持 - 处理中文文件名
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');

// 设置长时间执行 - Ghostscript可能需要较长时间
set_time_limit(300); // 5分钟
ini_set('memory_limit', '512M');

// 设置响应头为纯文本，便于在浏览器控制台查看
header('Content-Type: text/plain; charset=UTF-8');

// ========================================
// 目录路径设置
// ========================================
// 根据当前日期生成目录结构
$year = date("Y");
$month = date("m");
$week = date("d");

$fileplaceSHOW = "/".$year."/".$month."/".$week."/";   // 显示路径
$fileplace = "yes/".$year."/".$month."/".$week."/";    // 原始文件路径
$picplace = "yes/".$year."/".$month."/".$week."/preview/"; // 预览/线性化文件路径
$compressplace = "yes/".$year."/".$month."/".$week."/compress/"; // 压缩文件路径

// ========================================
// 请求验证
// ========================================
// 只允许POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Error: Only POST method allowed\n";
    exit;
}

// 获取JSON格式的POST数据
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['filename'])) {
    http_response_code(400);
    echo "Error: Missing filename parameter\n";
    exit;
}

$filename = $input['filename'];

// ========================================
// 中文文件名处理
// ========================================
// 检测并转换文件名编码，确保中文文件名正确处理
$detected_encoding = mb_detect_encoding($filename, ['UTF-8', 'GBK', 'GB2312'], true);
if ($detected_encoding !== 'UTF-8') {
    $filename = mb_convert_encoding($filename, 'UTF-8', $detected_encoding);
}

echo "=== PDF COMPRESSION PROCESSOR ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
echo "Processing file: " . $filename . "\n";

try {
    // ========================================
    // 第一步：创建压缩文件夹并确定文件路径
    // ========================================
    
    // 创建压缩文件夹（如果不存在）
    if (!file_exists($compressplace)) {
        if (!mkdir($compressplace, 0755, true)) {
            echo "Error: Failed to create compress directory: " . $compressplace . "\n";
            exit;
        }
        echo "Created compress directory: " . $compressplace . "\n";
    } else {
        echo "Compress directory already exists: " . $compressplace . "\n";
    }
    
    // 优先从preview目录读取线性化后的文件
    // 这是pdf_processor.php处理后的输出文件
    $linearized_file = $picplace . $filename;
    
    if (!file_exists($linearized_file)) {
        echo "Warning: Linearized file not found in preview directory\n";
        echo "Falling back to original file in main directory\n";
        
        // 如果preview目录没有文件，使用原始文件
        $linearized_file = $fileplace . $filename;
    }
    
    if (!file_exists($linearized_file)) {
        echo "Error: No source file found in either directory\n";
        exit;
    }
    
    echo "Target file: " . $linearized_file . "\n";
    
    // 设置压缩后的输出文件路径
    $compressed_file = $compressplace . $filename;
    echo "Compressed output file: " . $compressed_file . "\n";
    
    // ========================================
    // 第二步：执行Ghostscript压缩命令
    // ========================================
    
    // 注意：现在输入和输出是不同的文件
    // 输入：线性化文件（preview目录或原始目录）
    // 输出：压缩文件（compress目录）
    $gs_cmd = "gs -dNOPAUSE -dBATCH -dSAFER -dCompatibilityLevel=1.4 -dPDFSETTINGS=/prepress -dCompressFonts=true -dSubsetFonts=true -dCompressPages=true -dOptimize=true -sDEVICE=pdfwrite -sOutputFile=" . escapeshellarg($compressed_file) . " " . escapeshellarg($linearized_file) . " 2>&1";
    
    echo "Executing compression command...\n";
    echo "Command: " . $gs_cmd . "\n";
    
    // ========================================
    // 第三步：执行命令并记录输出
    // ========================================
    
    exec($gs_cmd, $gs_output, $gs_return_code);
    
    echo "Ghostscript return code: " . $gs_return_code . "\n";
    
    // 显示Ghostscript的详细输出（用于调试）
    if (!empty($gs_output)) {
        echo "Ghostscript output:\n";
        foreach ($gs_output as $line) {
            echo "  " . $line . "\n";
        }
    } else {
        echo "Ghostscript produced no output\n";
    }
    
    // ========================================
    // 第四步：验证压缩结果并显示文件信息
    // ========================================
    
    // 根据返回码判断是否成功
    // 0 = 成功，非0 = 有错误或警告
    if ($gs_return_code === 0) {
        echo "SUCCESS: Ghostscript compression completed successfully\n";
        
        // 检查压缩文件是否生成
        if (file_exists($compressed_file)) {
            $compressed_size = filesize($compressed_file);
            $original_size = filesize($linearized_file);
            
            echo "Compressed file created: " . $compressed_file . "\n";
            echo "Original file size: " . number_format($original_size) . " bytes\n";
            echo "Compressed file size: " . number_format($compressed_size) . " bytes\n";
            
            if ($original_size > 0) {
                $compression_ratio = round((($original_size - $compressed_size) / $original_size) * 100, 2);
                echo "Compression ratio: " . $compression_ratio . "%\n";
            }
        } else {
            echo "WARNING: Compressed file was not created\n";
        }
    } else {
        echo "WARNING: Ghostscript completed with return code " . $gs_return_code . "\n";
    }
    
    echo "=== COMPRESSION COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Exception during compression: " . $e->getMessage() . "\n";
}
?>
