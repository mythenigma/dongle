<?php
/**
 * 内存优化版PDF处理器
 * 专门针对10MB+文件的内存使用进行优化
 */

class MemoryOptimizedPDFProcessor {
    
    private $debugInfo = [];
    private $maxMemoryLimit;
    private $maxExecutionTime;
    
    public function __construct($memoryLimit = '256M', $timeLimit = 120) {
        $this->maxMemoryLimit = $memoryLimit;
        $this->maxExecutionTime = $timeLimit;
        
        // 设置PHP限制
        ini_set('memory_limit', $memoryLimit);
        ini_set('max_execution_time', $timeLimit);
    }
    
    /**
     * 主处理函数：根据文件大小和内存使用动态调整策略
     */
    public function processPDF($filePath, $maxProcessingTime = 100) {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        $fileSize = filesize($filePath);
        
        $this->debugInfo[] = "🔄 Starting memory-optimized PDF processing";
        $this->debugInfo[] = "📁 File size: " . $this->formatBytes($fileSize);
        $this->debugInfo[] = "🧠 Initial memory: " . $this->formatBytes($startMemory);
        
        // 根据文件大小选择处理策略
        $strategy = $this->getProcessingStrategy($fileSize);
        $this->debugInfo[] = "🎯 Selected strategy: " . $strategy;
        
        try {
            switch ($strategy) {
                case 'skip_optimization':
                    return $this->skipOptimization($filePath);
                    
                case 'linearize_only':
                    return $this->linearizeOnly($filePath, $maxProcessingTime);
                    
                case 'light_compress':
                    return $this->lightCompress($filePath, $maxProcessingTime);
                    
                case 'full_optimize':
                    return $this->fullOptimize($filePath, $maxProcessingTime);
                    
                default:
                    throw new Exception("Unknown processing strategy: " . $strategy);
            }
        } catch (Exception $e) {
            $this->debugInfo[] = "❌ Processing failed: " . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'debug' => $this->debugInfo,
                'memory_usage' => $this->getMemoryStats($startMemory),
                'processing_time' => microtime(true) - $startTime
            ];
        }
    }
    
    /**
     * 根据文件大小确定处理策略
     */
    private function getProcessingStrategy($fileSize) {
        // 检查当前可用内存
        $currentMemory = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit($this->maxMemoryLimit);
        $availableMemory = $memoryLimit - $currentMemory;
        
        $this->debugInfo[] = "🧠 Available memory: " . $this->formatBytes($availableMemory);
        
        // 基于文件大小的基础策略
        if ($fileSize > 50 * 1024 * 1024) {
            $baseStrategy = 'skip_optimization';
        } elseif ($fileSize > 30 * 1024 * 1024) {
            $baseStrategy = 'linearize_only';
        } elseif ($fileSize > 10 * 1024 * 1024) {
            $baseStrategy = 'light_compress';
        } else {
            $baseStrategy = 'full_optimize';
        }
        
        // 基于可用内存的调整
        $estimatedMemoryNeed = $fileSize * 4; // 估算需要4倍文件大小的内存
        
        if ($estimatedMemoryNeed > $availableMemory * 0.8) {
            // 内存不足，降级策略
            if ($baseStrategy === 'full_optimize') {
                $baseStrategy = 'light_compress';
                $this->debugInfo[] = "⬇️ Downgraded to light_compress due to memory constraints";
            } elseif ($baseStrategy === 'light_compress') {
                $baseStrategy = 'linearize_only';
                $this->debugInfo[] = "⬇️ Downgraded to linearize_only due to memory constraints";
            } elseif ($baseStrategy === 'linearize_only') {
                $baseStrategy = 'skip_optimization';
                $this->debugInfo[] = "⬇️ Downgraded to skip_optimization due to memory constraints";
            }
        }
        
        return $baseStrategy;
    }
    
    /**
     * 跳过优化：直接返回原文件
     */
    private function skipOptimization($filePath) {
        $this->debugInfo[] = "⚡ Skipping optimization for large file (>50MB)";
        
        return [
            'success' => true,
            'optimized' => false,
            'compressed' => false,
            'linearized' => false,
            'file_path' => $filePath,
            'message' => 'File too large, optimization skipped for performance',
            'debug' => $this->debugInfo
        ];
    }
    
    /**
     * 仅线性化：跳过压缩，只做线性化
     */
    private function linearizeOnly($filePath, $maxTime) {
        $startTime = microtime(true);
        $this->debugInfo[] = "📐 Starting linearization-only processing";
        
        $result = $this->performLinearization($filePath, $maxTime - (microtime(true) - $startTime));
        
        return [
            'success' => $result['success'],
            'optimized' => $result['success'],
            'compressed' => false,
            'linearized' => $result['success'],
            'file_path' => $filePath,
            'message' => $result['success'] ? 'PDF linearized successfully' : 'Linearization failed',
            'debug' => $this->debugInfo,
            'processing_time' => microtime(true) - $startTime
        ];
    }
    
    /**
     * 轻量压缩：使用内存友好的压缩参数
     */
    private function lightCompress($filePath, $maxTime) {
        $startTime = microtime(true);
        $this->debugInfo[] = "🪶 Starting light compression processing";
        
        // 轻量压缩（内存优化）
        $compressResult = $this->performLightCompression($filePath, $maxTime * 0.6);
        
        if (!$compressResult['success']) {
            $this->debugInfo[] = "⚠️ Light compression failed, proceeding with linearization only";
        }
        
        // 线性化
        $remainingTime = $maxTime - (microtime(true) - $startTime);
        if ($remainingTime > 10) { // 至少保留10秒给线性化
            $linearizeResult = $this->performLinearization($filePath, $remainingTime);
        } else {
            $linearizeResult = ['success' => false, 'reason' => 'Insufficient time remaining'];
            $this->debugInfo[] = "⏰ Skipping linearization due to time constraints";
        }
        
        return [
            'success' => true,
            'optimized' => $compressResult['success'] || $linearizeResult['success'],
            'compressed' => $compressResult['success'],
            'linearized' => $linearizeResult['success'],
            'file_path' => $filePath,
            'message' => 'PDF processed with light compression strategy',
            'debug' => $this->debugInfo,
            'processing_time' => microtime(true) - $startTime
        ];
    }
    
    /**
     * 完整优化：标准压缩+线性化
     */
    private function fullOptimize($filePath, $maxTime) {
        $startTime = microtime(true);
        $this->debugInfo[] = "🔧 Starting full optimization processing";
        
        // 标准压缩
        $compressResult = $this->performStandardCompression($filePath, $maxTime * 0.7);
        
        // 线性化
        $remainingTime = $maxTime - (microtime(true) - $startTime);
        if ($remainingTime > 10) {
            $linearizeResult = $this->performLinearization($filePath, $remainingTime);
        } else {
            $linearizeResult = ['success' => false, 'reason' => 'Insufficient time remaining'];
            $this->debugInfo[] = "⏰ Skipping linearization due to time constraints";
        }
        
        return [
            'success' => true,
            'optimized' => $compressResult['success'] || $linearizeResult['success'],
            'compressed' => $compressResult['success'],
            'linearized' => $linearizeResult['success'],
            'file_path' => $filePath,
            'message' => 'PDF fully optimized',
            'debug' => $this->debugInfo,
            'processing_time' => microtime(true) - $startTime
        ];
    }
    
    /**
     * 执行轻量压缩（内存优化版本）
     */
    private function performLightCompression($filePath, $maxTime) {
        $startTime = microtime(true);
        $beforeMemory = memory_get_usage(true);
        
        $compressedFile = $filePath . '.light_compressed';
        
        // 使用内存友好的Ghostscript参数
        $gsCommand = "gs -sDEVICE=pdfwrite " .
                    "-dCompatibilityLevel=1.4 " .
                    "-dPDFSETTINGS=/screen " .           // 更激进的压缩
                    "-dNOPAUSE -dQUIET -dBATCH " .
                    "-dMaxBitmap=30000000 " .            // 限制位图内存到30MB
                    "-dDownsampleColorImages=true " .    // 降采样彩色图像
                    "-dColorImageResolution=120 " .      // 降低分辨率到120dpi
                    "-dDownsampleGrayImages=true " .     // 降采样灰度图像
                    "-dGrayImageResolution=120 " .       // 灰度图像120dpi
                    "-dDownsampleMonoImages=true " .     // 降采样单色图像
                    "-dMonoImageResolution=300 " .       // 单色图像300dpi
                    "-sOutputFile=" . escapeshellarg($compressedFile) . " " .
                    escapeshellarg($filePath) . " 2>&1";
        
        $this->debugInfo[] = "🪶 Light compression command: " . substr($gsCommand, 0, 100) . "...";
        
        exec($gsCommand, $output, $returnCode);
        
        $processingTime = microtime(true) - $startTime;
        $memoryUsed = memory_get_usage(true) - $beforeMemory;
        $peakMemory = memory_get_peak_usage(true);
        
        $this->debugInfo[] = "🧠 Compression memory used: " . $this->formatBytes($memoryUsed);
        $this->debugInfo[] = "📊 Peak memory: " . $this->formatBytes($peakMemory);
        $this->debugInfo[] = "⏱️ Compression time: " . round($processingTime, 2) . "s";
        
        if ($returnCode === 0 && file_exists($compressedFile)) {
            if ($this->validateCompressedFile($compressedFile, $filePath)) {
                if (rename($compressedFile, $filePath)) {
                    return ['success' => true, 'memory_used' => $memoryUsed];
                }
            }
            if (file_exists($compressedFile)) unlink($compressedFile);
        }
        
        $this->debugInfo[] = "⚠️ Light compression failed: " . implode(' ', $output);
        return ['success' => false, 'memory_used' => $memoryUsed];
    }
    
    /**
     * 执行标准压缩
     */
    private function performStandardCompression($filePath, $maxTime) {
        $startTime = microtime(true);
        $beforeMemory = memory_get_usage(true);
        
        $compressedFile = $filePath . '.compressed';
        
        $gsCommand = "gs -sDEVICE=pdfwrite " .
                    "-dCompatibilityLevel=1.4 " .
                    "-dPDFSETTINGS=/ebook " .
                    "-dNOPAUSE -dQUIET -dBATCH " .
                    "-sOutputFile=" . escapeshellarg($compressedFile) . " " .
                    escapeshellarg($filePath) . " 2>&1";
        
        exec($gsCommand, $output, $returnCode);
        
        $processingTime = microtime(true) - $startTime;
        $memoryUsed = memory_get_usage(true) - $beforeMemory;
        
        $this->debugInfo[] = "🧠 Standard compression memory: " . $this->formatBytes($memoryUsed);
        $this->debugInfo[] = "⏱️ Standard compression time: " . round($processingTime, 2) . "s";
        
        if ($returnCode === 0 && file_exists($compressedFile)) {
            if ($this->validateCompressedFile($compressedFile, $filePath)) {
                if (rename($compressedFile, $filePath)) {
                    return ['success' => true, 'memory_used' => $memoryUsed];
                }
            }
            if (file_exists($compressedFile)) unlink($compressedFile);
        }
        
        return ['success' => false, 'memory_used' => $memoryUsed];
    }
    
    /**
     * 执行线性化
     */
    private function performLinearization($filePath, $maxTime) {
        $startTime = microtime(true);
        $beforeMemory = memory_get_usage(true);
        
        $linearizedFile = $filePath . '.linearized';
        
        $qpdfCommand = "qpdf --linearize " . 
                      escapeshellarg($filePath) . " " . 
                      escapeshellarg($linearizedFile) . " 2>&1";
        
        exec($qpdfCommand, $output, $returnCode);
        
        $processingTime = microtime(true) - $startTime;
        $memoryUsed = memory_get_usage(true) - $beforeMemory;
        
        $this->debugInfo[] = "🧠 Linearization memory: " . $this->formatBytes($memoryUsed);
        $this->debugInfo[] = "⏱️ Linearization time: " . round($processingTime, 2) . "s";
        
        if ($returnCode === 0 && file_exists($linearizedFile)) {
            if ($this->validateLinearizedFile($linearizedFile, $filePath)) {
                if (rename($linearizedFile, $filePath)) {
                    return ['success' => true, 'memory_used' => $memoryUsed];
                }
            }
            if (file_exists($linearizedFile)) unlink($linearizedFile);
        }
        
        return ['success' => false, 'memory_used' => $memoryUsed];
    }
    
    /**
     * 验证压缩后的文件
     */
    private function validateCompressedFile($compressedFile, $originalFile) {
        $compressedSize = filesize($compressedFile);
        $originalSize = filesize($originalFile);
        
        // 基本大小检查
        if ($compressedSize < 1000) {
            $this->debugInfo[] = "❌ Compressed file too small: " . $compressedSize . " bytes";
            return false;
        }
        
        // PDF头检查
        $header = file_get_contents($compressedFile, false, null, 0, 8);
        if (!$header || strpos($header, '%PDF-') !== 0) {
            $this->debugInfo[] = "❌ Invalid PDF header in compressed file";
            return false;
        }
        
        // 简单的可读性检查
        $testCommand = "qpdf --show-npages " . escapeshellarg($compressedFile) . " 2>&1";
        exec($testCommand, $testOutput, $testReturnCode);
        
        if ($testReturnCode !== 0) {
            $this->debugInfo[] = "❌ Compressed file readability test failed";
            return false;
        }
        
        $pageCount = intval(trim(implode('', $testOutput)));
        if ($pageCount <= 0) {
            $this->debugInfo[] = "❌ Compressed file has no pages";
            return false;
        }
        
        $this->debugInfo[] = "✅ Compressed file validated: " . $pageCount . " pages, " . 
                           $this->formatBytes($compressedSize);
        return true;
    }
    
    /**
     * 验证线性化后的文件
     */
    private function validateLinearizedFile($linearizedFile, $originalFile) {
        $linearizedSize = filesize($linearizedFile);
        $originalSize = filesize($originalFile);
        
        // 基本大小检查
        if ($linearizedSize < 1000) {
            $this->debugInfo[] = "❌ Linearized file too small: " . $linearizedSize . " bytes";
            return false;
        }
        
        // 检查线性化标记
        $header = file_get_contents($linearizedFile, false, null, 0, 1024);
        if (!$header || strpos($header, '/Linearized') === false) {
            $this->debugInfo[] = "❌ File not properly linearized";
            return false;
        }
        
        $this->debugInfo[] = "✅ Linearized file validated: " . $this->formatBytes($linearizedSize);
        return true;
    }
    
    /**
     * 获取内存使用统计
     */
    private function getMemoryStats($startMemory) {
        return [
            'start_memory' => $startMemory,
            'current_memory' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'memory_used' => memory_get_usage(true) - $startMemory,
            'memory_limit' => $this->parseMemoryLimit($this->maxMemoryLimit)
        ];
    }
    
    /**
     * 解析内存限制字符串
     */
    private function parseMemoryLimit($memoryLimit) {
        $memoryLimit = strtoupper($memoryLimit);
        $multiplier = 1;
        
        if (strpos($memoryLimit, 'G') !== false) {
            $multiplier = 1024 * 1024 * 1024;
        } elseif (strpos($memoryLimit, 'M') !== false) {
            $multiplier = 1024 * 1024;
        } elseif (strpos($memoryLimit, 'K') !== false) {
            $multiplier = 1024;
        }
        
        return intval($memoryLimit) * $multiplier;
    }
    
    /**
     * 格式化字节数为可读格式
     */
    private function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
    
    /**
     * 获取调试信息
     */
    public function getDebugInfo() {
        return $this->debugInfo;
    }
}

// 使用示例
/*
$processor = new MemoryOptimizedPDFProcessor('256M', 120);
$result = $processor->processPDF('/path/to/large.pdf', 100);

if ($result['success']) {
    echo "PDF processed successfully!\n";
    echo "Compressed: " . ($result['compressed'] ? 'Yes' : 'No') . "\n";
    echo "Linearized: " . ($result['linearized'] ? 'Yes' : 'No') . "\n";
    echo "Processing time: " . $result['processing_time'] . "s\n";
} else {
    echo "Processing failed: " . $result['error'] . "\n";
}

// 打印调试信息
foreach ($processor->getDebugInfo() as $debug) {
    echo $debug . "\n";
}
*/
?>
