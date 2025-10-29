<?php
// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 主要服装数据定义
$clothingData = array(
    'shorts_set' => array(
        'name' => '短裤套装',
        'top' => array(
            'S' => 1037,
            'M' => 1369,
            'L' => 1432,
            'XL' => 1506,
            'XXL' => 1553
        ),
        'bottom' => array(
            'name' => '短裤',
            'S' => 861,
            'M' => 830,
            'L' => 913,
            'XL' => 941,
            'XXL' => 948
        )
    ),
    'pants_set' => array(
        'name' => '长裤套装',
        'top' => array(
            'M' => 1037,
            'L' => 1369,
            'XL' => 1432,
            'XXL' => 1506
        ),
        'bottom' => array(
            'name' => '长裤',
            'M' => 1402,
            'L' => 1493,
            'XL' => 1589,
            'XXL' => 1666
        )
    ),
    'chinese_suit' => array(
        'name' => '中山装',
        'top' => array(
            'M' => 1916,
            'L' => 1939,
            'XL' => 1951,
            'XXL' => 2046
        ),
        'bottom' => array(
            'name' => '长裤',
            'M' => 1402,
            'L' => 1493,
            'XL' => 1589,
            'XXL' => 1666
        )
    )
);

// 额外服装数据
$extraClothingData = array(
    'polo_set' => array(
        'name' => 'POLO杉套装',
        'top' => array(
            'S' => 790,  // 后续可添加实际长度
            'M' => 864,  // 后续可添加实际长度
            'L' => 1061,  // 后续可添加实际长度
            'XL' => 1070, // 后续可添加实际长度
            'XXL' => 1142 // 后续可添加实际长度
        ),
        'bottom' => array(
            'name' => '短裤',
            'S' => 871,  // 修正：移除了重复的S键定义
            'M' => 841,  // 后续可添加实际长度
            'L' => 925,  // 后续可添加实际长度
            'XL' => 952, // 后续可添加实际长度
            'XXL' => 959 // 后续可添加实际长度
        )
    ),
    'sweatshirt_set' => array(
        'name' => '卫衣束脚裤套装',
        'top' => array(
            'S' => 1286,  // 后续可添加实际长度
            'M' => 1370,  // 后续可添加实际长度
            'L' => 1538,  // 后续可添加实际长度
            'XL' => 1530, // 后续可添加实际长度
            'XXL' => 1604 // 后续可添加实际长度
        ),
        'bottom' => array(
            'name' => '束脚裤',
            'S' => 1355,  // 后续可添加实际长度
            'M' => 1415,  // 后续可添加实际长度
            'L' => 1470,  // 后续可添加实际长度
            'XL' => 1515, // 后续可添加实际长度
            'XXL' => 1563 // 后续可添加实际长度
        )
    ),
    // 单独长裤添加到额外服装数据中
    'single_pants' => array(
        'name' => '改版长裤',
        'pants' => array(
            'M' => 1402,    // 后续添加实际长度
            'L' => 1493,    // 后续添加实际长度
            'XL' => 1589,   // 后续添加实际长度
            'XXL' => 1666,  // 后续添加实际长度
            '3XL' => 1741   // 后续添加实际长度
        )
    ),
    // 添加老版长裤
    'old_pants' => array(
        'name' => '老版长裤',
        'pants' => array(
            'M' => 1522,    // 后续添加实际长度
            'L' => 1607,    // 后续添加实际长度
            'XL' => 1714,   // 后续添加实际长度
            'XXL' => 1796,  // 后续添加实际长度
            '3XL' => 1892   // 后续添加实际长度
        )
    )
);

// 合并所有服装数据用于计算
$allClothingData = array_merge($clothingData, $extraClothingData);

// 尺码列表（普通款式，不含3XL）
$sizes = array('S', 'M', 'L', 'XL', 'XXL');
// 单独长裤尺码列表（包含3XL，不包含S码）
$singlePantsSizes = array('M', 'L', 'XL', 'XXL', '3XL');

// 初始化变量
$totalLength = 0;
$totalLengthMeters = 0;
$overLimit = false;
$resultClass = 'under-limit';
$extraVisible = false; // 记录额外套装是否可见

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 检查是否是重置操作
    if (isset($_POST['reset_form'])) {
        // 保持下拉框状态
        $extraVisible = isset($_POST['extra_visible']) && $_POST['extra_visible'] == '1';
    } else {
        // 计算总长度（包括额外套装）
        foreach ($allClothingData as $itemKey => $itemDetails) {
            // 处理普通套装的上衣
            if (isset($itemDetails['top'])) {
                foreach ($itemDetails['top'] as $size => $length) {
                    $inputField = $itemKey . '_top_' . $size;
                    $quantity = 0;
                    if (isset($_POST[$inputField]) && $_POST[$inputField] != '') {
                        $quantity = intval($_POST[$inputField]);
                    }
                    $totalLength += $quantity * $length;
                }
            }
            
            // 处理普通套装的下装
            if (isset($itemDetails['bottom'])) {
                foreach ($itemDetails['bottom'] as $size => $length) {
                    if ($size != 'name') {
                        $inputField = $itemKey . '_bottom_' . $size;
                        $quantity = 0;
                        if (isset($_POST[$inputField]) && $_POST[$inputField] != '') {
                            $quantity = intval($_POST[$inputField]);
                        }
                        $totalLength += $quantity * $length;
                    }
                }
            }
            
            // 处理单独长裤（仅这一项有3XL）
            if (isset($itemDetails['pants']) && ($itemKey == 'single_pants' || $itemKey == 'old_pants')) {
                foreach ($itemDetails['pants'] as $size => $length) {
                    $inputField = $itemKey . '_' . $size;
                    $quantity = 0;
                    if (isset($_POST[$inputField]) && $_POST[$inputField] != '') {
                        $quantity = intval($_POST[$inputField]);
                    }
                    $totalLength += $quantity * $length;
                }
            }
        }
        
        // 计算米数
        $totalLengthMeters = round($totalLength / 1000, 2);
        
        // 判断是否超限
        if ($totalLength > 134000) {
            $overLimit = true;
            $resultClass = 'over-limit';
        } else {
            $overLimit = false;
            $resultClass = 'under-limit';
        }
        
        // 保持下拉框状态
        $extraVisible = isset($_POST['extra_visible']) && $_POST['extra_visible'] == '1';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-
scale=1.0, user-scalable=no">
    <title>随便</title>
	<link rel="shortcut icon" href="chenyuqiang.png" type="image/png">
    <style>
        /* 基础样式 - 手机优化 */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        /* 雪花背景容器 */
        body {
            font-family: Arial, sans-serif;
            color: #333;
            padding: 10px;
            font-size: 14px;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* 雪花Canvas样式 */
        #snowCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
        
        /* 背景渐变和装饰 */
        .background-effect {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4eaf1 100%);
            z-index: -2;
        }
        
        .background-effect::before,
        .background-effect::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            animation: float 15s infinite ease-in-out;
        }
        
        .background-effect::before {
            width: 200px;
            height: 200px;
            top: 10%;
            right: -100px;
        }
        
        .background-effect::after {
            width: 300px;
            height: 300px;
            bottom: -150px;
            left: -100px;
            animation-delay: 5s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(5deg);
            }
        }
        
        .container {
            width: 100%;
            background-color: rgba(255, 255, 255, 0);
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            //backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* 标题和提示按钮样式 */
        .header-container {
            position: relative;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        h1 {
            color: #333;
            text-align: center;
            font-size: 1.4rem;
            margin: 0;
        }
        
        h1::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #4CAF50, #2ecc71);
        }
        
        /* 按钮容器样式 */
        .header-buttons {
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            display: flex;
            gap: 8px;
        }
        
        /* 提示按钮样式 */
        .notice-btn {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #000000;
            color: white;
            border: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* 跳转按钮样式 */
        .jump-btn {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #4CAF50;
            color: white;
            border: none;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* 半透明提示窗口样式 */
        .notice-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(1.5px);
        }
        
        .notice-window {
            background-color: rgba(255, 255, 255, 0.95);
            width: 85%;
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .close-notice {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #777;
            padding: 5px;
        }
        
        .notice-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: #000000;
            text-align: center;
        }
        
        .notice-content {
            font-size: 0.9rem;
            line-height: 1.6;
            color: #555;
        }
        
        /* 表格样式 - 手机优化 */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .main-table th, .main-table td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
            font-size: 0.85rem;
        }
        
        .main-table th {
            background-color: rgba(240, 240, 240, 0.8);
            font-weight: bold;
        }
        
        /* 套装间隔样式 */
        .category-group {
            border-bottom: 2px solid #ccc;
        }
        
        /* 最后一个套装移除底部边框 */
        .category-group:last-child {
            border-bottom: none;
        }
        
        .category-row {
            background-color: rgba(249, 249, 249, 0.9);
        }
        
        /* 修改款式名称颜色为明显的提示色 */
        .category-title {
            text-align: left;
            padding-left: 10px;
            font-weight: bold;
            color: #ff3300; /* 改为醒目的红色 */
            font-size: 0.95rem; /* 稍微增大字体 */
        }
        
        /* 输入框样式 */
        input[type="number"] {
            width: 45px;
            padding: 4px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        input[type="number"]:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }
        
        /* 按钮样式 */
        .btn-group {
            text-align: center;
            margin: 15px 0;
            padding: 10px 0;
        }
        
        button {
            padding: 10px 20px;
            margin: 0 8px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: bold;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }
        
        button:hover::after {
            width: 200px;
            height: 200px;
        }
        
        .calculate {
            background-color: #4CAF50;
            color: white;
        }
        
        .reset {
            background-color: #f44336;
            color: white;
        }
        
        /* 额外套装下拉框样式 */
        .extra-suits {
            margin: 15px 0;
        }
        
        .toggle-extra {
            background-color: #000000;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 0.9rem;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .toggle-icon {
            transition: transform 0.3s ease;
        }
        
        .rotate {
            transform: rotate(180deg);
        }
        
        .extra-content {
            display: none;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
        }
        
        /* 结果区域样式 */
        .result {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
            background-color: rgba(249, 249, 249, 0.9);
            border-radius: 8px;
            transition: all 0.5s ease;
        }
        
        .over-limit {
            color: red;
        }
        
        .under-limit {
            color: #2ecc71;
        }
        
        .result-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 8px 0;
        }
        
        footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
            font-size: 0.8rem;
        }

        /* 单独长裤模块样式 */
        .single-pants-note {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
            text-align: right;
            font-style: italic;
        }
        
        /* 单独长裤表格样式 */
        .single-pants-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .single-pants-table th,
        .single-pants-table td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <!-- 雪花Canvas元素 -->
    <canvas id="snowCanvas"></canvas>
    
    <!-- 背景特效元素 -->
    <div class="background-effect"></div>
    
    <div class="container">
        <!-- 标题和提示按钮容器 -->
        <div class="header-container">
            <h1>随便</h1>
            <div class="header-buttons">
                <!-- 跳转按钮 -->
                <button class="jump-btn" id="jumpBtn" title="跳转到其他页面">→</button>
                <!-- 提示按钮 -->
                <button class="notice-btn" id="noticeBtn">i</button>
            </div>
        </div>
        
        <!-- 提示公告窗口 -->
        <div class="notice-overlay" id="noticeOverlay">
            <div class="notice-window" style="
                background-image: url('dadan.jpg'); /* 添加背景图片 */
                background-size: cover; /* 背景图片自适应窗口大小 */
                background-position: center; /* 背景图片居中显示 */
                background-repeat: no-repeat; /* 背景图片不重复 */
            ">
                <!-- 以下是添加的注释备注：
                    1. 为notice-window添加了背景图片样式
                    2. 使用了picsum.photos提供的示例图片，实际使用时可替换为具体图片URL
                    3. 通过background-size、background-position和background-repeat属性优化
图片显示效果
                -->
                <button class="close-notice" id="closeNotice">&times;</button>
                <h3 class="notice-title">随便</h3>
                <div class="notice-content">
                    <p>1、束脚裤卫衣套装与单束脚裤通用！</p>
					<p>2、       </p>
					<p>3、                       </p>
					<p>4、                       </p>
					<p>5、                       </p>
					<p>6、                       </p>
					<p>7、                       </p>
					<p>8、                       </p>
                </div>
            </div>
        </div>
        
        <form method="post" id="calculatorForm">
            <!-- 隐藏字段用于保存下拉框状态 -->
            <input type="hidden" name="extra_visible" id="extraVisibleInput" value="0">
            
            <table class="main-table">
                <tr>
                    <th rowspan="2">款式</th>
                    <th rowspan="2">衣裤</th>
                    <?php foreach ($sizes as $size): ?>
                        <th><?php echo $size; ?>码</th>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <?php foreach ($sizes as $size): ?>
                        <th>件数</th>
                    <?php endforeach; ?>
                </tr>
                
                <?php 
                $totalItems = count($clothingData);
                $currentItem = 0;
                foreach ($clothingData as $itemKey => $itemDetails): 
                    $currentItem++;
                ?>
                    <!-- 套装分组，添加间隔线 -->
                    <tbody class="category-group">
                        <!-- 上衣行 -->
                        <tr class="category-row">
                            <td class="category-title" rowspan="2"><?php echo $itemDetails
['name']; ?></td>
                            <td>上衣</td>
                            
                            <?php foreach ($sizes as $size): ?>
                                <td>
                                    <?php
                                    $inputName = $itemKey . '_top_' . $size;
                                    $hasSize = 0;
                                    
                                    if (isset($itemDetails['top'][$size])) {
                                        $hasSize = 1;
                                    }
                                    
                                    if ($hasSize == 1) {
                                        $value = '';
                                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset
($_POST['reset_form']) && isset($_POST[$inputName]) && $_POST[$inputName] != '') {
                                            $value = intval($_POST[$inputName]);
                                        }
                                        echo '<input type="number" name="' . $inputName . 
'" min="0" value="' . $value . '">';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        
                        <!-- 下装行 -->
                        <tr>
                            <td><?php echo $itemDetails['bottom']['name']; ?></td>
                            
                            <?php foreach ($sizes as $size): ?>
                                <td>
                                    <?php
                                    $inputName = $itemKey . '_bottom_' . $size;
                                    $hasSize = 0;
                                    
                                    if (isset($itemDetails['bottom'][$size])) {
                                        $hasSize = 1;
                                    }
                                    
                                    if ($hasSize == 1) {
                                        $value = '';
                                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset
($_POST['reset_form']) && isset($_POST[$inputName]) && $_POST[$inputName] != '') {
                                            $value = intval($_POST[$inputName]);
                                        }
                                        echo '<input type="number" name="' . $inputName . 
'" min="0" value="' . $value . '">';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
            
            <!-- 额外套装下拉框 -->
            <div class="extra-suits">
                <button type="button" class="toggle-extra" id="toggleExtraBtn">
                    POLO衫套装、卫衣束脚裤套装、改版长裤、老版长裤
                    <span class="toggle-icon">▼</span>
                </button>
                
                <div class="extra-content" id="extraContent">
                    <!-- POLO衫套装和卫衣束脚裤套装表格（不含3XL） -->
                    <table class="main-table">
                        <tr>
                            <th rowspan="2">款式</th>
                            <th rowspan="2">衣裤</th>
                            <?php foreach ($sizes as $size): ?>
                                <th><?php echo $size; ?>码</th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <?php foreach ($sizes as $size): ?>
                                <th>件数</th>
                            <?php endforeach; ?>
                        </tr>
                        
                        <?php 
                        foreach ($extraClothingData as $itemKey => $itemDetails): 
                            // 只显示POLO衫套装和卫衣束脚裤套装
                            if ($itemKey != 'single_pants' && $itemKey != 'old_pants'):
                        ?>
                            <!-- 额外套装分组 -->
                            <tbody class="category-group">
                                <!-- 上衣行 -->
                                <tr class="category-row">
                                    <td class="category-title" rowspan="2"><?php echo 
$itemDetails['name']; ?></td>
                                    <td>
                                        <?php 
                                        // 根据套装名称确定上衣类型显示
                                        if ($itemKey == 'polo_set') {
                                            echo 'POLO杉';
                                        } else if ($itemKey == 'sweatshirt_set') {
                                            echo '卫衣';
                                        } else {
                                            echo '上衣';
                                        }
                                        ?>
                                    </td>
                                    
                                    <?php foreach ($sizes as $size): ?>
                                        <td>
                                            <?php
                                            $inputName = $itemKey . '_top_' . $size;
                                            $hasSize = 0;
                                            
                                            if (isset($itemDetails['top'][$size])) {
                                                $hasSize = 1;
                                            }
                                            
                                            if ($hasSize == 1) {
                                                $value = '';
                                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && 
!isset($_POST['reset_form']) && isset($_POST[$inputName]) && $_POST[$inputName] != '') {
                                                    $value = intval($_POST[$inputName]);
                                                }
                                                echo '<input type="number" name="' . 
$inputName . '" min="0" value="' . $value . '">';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                
                                <!-- 下装行 -->
                                <tr>
                                    <td><?php echo $itemDetails['bottom']['name']; ?></td>
                                    
                                    <?php foreach ($sizes as $size): ?>
                                        <td>
                                            <?php
                                            $inputName = $itemKey . '_bottom_' . $size;
                                            $hasSize = 0;
                                            
                                            if (isset($itemDetails['bottom'][$size])) {
                                                $hasSize = 1;
                                            }
                                            
                                            if ($hasSize == 1) {
                                                $value = '';
                                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && 
!isset($_POST['reset_form']) && isset($_POST[$inputName]) && $_POST[$inputName] != '') {
                                                    $value = intval($_POST[$inputName]);
                                                }
                                                echo '<input type="number" name="' . 
$inputName . '" min="0" value="' . $value . '">';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </table>
                    
                    <!-- 单独长裤专用表格（含3XL，不含S码） -->
                    <table class="single-pants-table">
                        <tr>
                            <th>款式</th>
                            <?php foreach ($singlePantsSizes as $size): ?>
                                <th><?php echo $size; ?>码</th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td class="category-title"><?php echo $extraClothingData['single_pants']['name']; ?></td>
                            <?php foreach ($singlePantsSizes as $size): ?>
                                <td>
                                    <?php
                                    $inputName = 'single_pants_' . $size;
                                    $value = '';
                                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset
($_POST['reset_form']) && isset($_POST[$inputName]) && $_POST[$inputName] != '') {
                                        $value = intval($_POST[$inputName]);
                                    }
                                    echo '<input type="number" name="' . $inputName . '" 
min="0" value="' . $value . '">';
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </table>
                    
                    <!-- 老版长裤专用表格（含3XL，不含S码） -->
                    <table class="single-pants-table">
                        <tr>
                            <th>款式</th>
                            <?php foreach ($singlePantsSizes as $size): ?>
                                <th><?php echo $size; ?>码</th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td class="category-title"><?php echo $extraClothingData['old_pants']['name']; ?></td>
                            <?php foreach ($singlePantsSizes as $size): ?>
                                <td>
                                    <?php
                                    $inputName = 'old_pants_' . $size;
                                    $value = '';
                                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset
($_POST['reset_form']) && isset($_POST[$inputName]) && $_POST[$inputName] != '') {
                                        $value = intval($_POST[$inputName]);
                                    }
                                    echo '<input type="number" name="' . $inputName . '" 
min="0" value="' . $value . '">';
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td colspan="<?php echo count($singlePantsSizes) + 1; ?>" 
class="single-pants-note">
                                <!--备注：老版长裤各尺码长度后续将添加实际数据（当前为0毫米）-->
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="calculate" name="calculate">计算</button>
                <button type="submit" class="reset" name="reset_form">重置</button>
            </div>
        </form>
        
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['reset_form'])): ?>
            <div class="result">
                <h2>计算结果：</h2>
                <p class="result-value <?php echo $resultClass; ?>">
                    <?php echo number_format($totalLength); ?> 毫米
                </p>
                <p class="result-value">
                    <?php echo $totalLengthMeters; ?> 米
                </p>
                
                <?php if ($overLimit == true): ?>
                    <p class="over-limit">注意：长度已超过134米</p>
                <?php else: ?>
                    <p class="under-limit"></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <footer>
            <p>强 &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>

    <script>
        // 等待DOM加载完成后执行
        document.addEventListener('DOMContentLoaded', function() {
            // 雪花飘落效果实现
            var snowCanvas = document.getElementById("snowCanvas");
            if (snowCanvas && snowCanvas.getContext) {
                var snowContext = snowCanvas.getContext('2d');
                var w = window.innerWidth;
                var h = window.innerHeight;
                snowCanvas.width = w;
                snowCanvas.height = h;

                // 监听窗口大小变化，调整Canvas尺寸
                window.addEventListener('resize', function() {
                    w = window.innerWidth;
                    h = window.innerHeight;
                    snowCanvas.width = w;
                    snowCanvas.height = h;
                });

                var num = 100;
                var snows = [];
                for(var i = 0; i < num; i++){
                    snows.push({
                        x: Math.random() * w,
                        y: Math.random() * h,
                        r: Math.random() * 5 + 1,
                        dx: Math.random() * 1 + 0.5,
                        dy: Math.random() * 1 + 0.5
                    });
                }
                
                function moveSnow() {
                    for(var i = 0; i < num; i++){
                        var snow = snows[i];
                        snow.x += snow.dx;
                        snow.y += snow.dy;
                        
                        // 雪花超出边界后重新放置到顶部
                        if (snow.x > w) snow.x = 0;
                        if (snow.x < 0) snow.x = w;
                        if (snow.y > h) snow.y = 0;
                        if (snow.y < 0) snow.y = h;
                    }
                }
                
                function drawSnow() {
                    // 清除画布
                    snowContext.clearRect(0, 0, w, h);
                    
                    // 绘制雪花
                    snowContext.beginPath();
                    snowContext.fillStyle = 'rgb(255, 255, 255)';
                    snowContext.shadowColor = 'rgb(255, 255, 255)';
                    snowContext.shadowBlur = 10;

                    for(var i = 0; i < num; i++){
                        var snow = snows[i];
                        snowContext.moveTo(snow.x, snow.y);
                        snowContext.arc(snow.x, snow.y, snow.r, 0, Math.PI * 2);
                    }
                    
                    snowContext.fill();
                    snowContext.closePath();
                    
                    // 移动雪花位置
                    moveSnow();
                    
                    // 使用requestAnimationFrame实现平滑动画
                    requestAnimationFrame(drawSnow);
                }
                
                // 启动雪花动画
                drawSnow();
            }

            // 提示公告窗口功能
            var noticeBtn = document.getElementById('noticeBtn');
            var noticeOverlay = document.getElementById('noticeOverlay');
            var closeNotice = document.getElementById('closeNotice');
            
            if (noticeBtn && noticeOverlay && closeNotice) {
                noticeBtn.addEventListener('click', function() {
                    noticeOverlay.style.display = 'flex';
                });
                
                closeNotice.addEventListener('click', function() {
                    noticeOverlay.style.display = 'none';
                });
                
                // 点击遮罩层关闭窗口
                noticeOverlay.addEventListener('click', function(e) {
                    if (e.target === noticeOverlay) {
                        noticeOverlay.style.display = 'none';
                    }
                });
            }
            
            // 跳转按钮功能
            var jumpBtn = document.getElementById('jumpBtn');
            if (jumpBtn) {
                jumpBtn.addEventListener('click', function() {
                    // 后续添加实际页面地址
                    // 例如：window.location.href = 'https://example.com/target-page';
                    alert('😂');
                    window.location.href = 'qiang.html';
                    
                    /* 
                     * 跳转功能说明：
                     * 1. 取消下面的注释并替换为实际页面URL
                     * 2. 示例：window.location.href = 'https://example.com/target-page';
                     */
                    // window.location.href = '后续添加实际页面地址';
                });
            }
            
            // 下拉框功能
            var toggleBtn = document.getElementById('toggleExtraBtn');
            var extraContent = document.getElementById('extraContent');
            var toggleIcon = document.querySelector('.toggle-icon');
            var extraVisibleInput = document.getElementById('extraVisibleInput');
            <?php if ($extraVisible): ?>
                var isExtraVisible = true;
            <?php else: ?>
                var isExtraVisible = false;
            <?php endif; ?>
            
            if (toggleBtn && extraContent && toggleIcon && extraVisibleInput) {
                // 设置初始状态
                if (isExtraVisible) {
                    extraContent.style.display = 'block';
                    toggleIcon.classList.add('rotate');
                    extraVisibleInput.value = '1';
                }
                
                toggleBtn.addEventListener('click', function() {
                    if (extraContent.style.display === 'none' || extraContent.style.display 
=== '') {
                        extraContent.style.display = 'block';
                        toggleIcon.classList.add('rotate');
                        extraVisibleInput.value = '1';
                    } else {
                        extraContent.style.display = 'none';
                        toggleIcon.classList.remove('rotate');
                        extraVisibleInput.value = '0';
                    }
                });
            }
            
            // 重置功能
            var resetBtn = document.querySelector('button[name="reset_form"]');
            var form = document.getElementById('calculatorForm');
            
            if (resetBtn && form) {
                resetBtn.onclick = function(e) {
                    e.preventDefault();
                    
                    // 清除所有输入框的值
                    var inputs = document.querySelectorAll('input[type="number"]');
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].value = '';
                    }
                    
                    // 隐藏结果区域
                    var resultDiv = document.querySelector('.result');
                    if (resultDiv) {
                        resultDiv.style.display = 'none';
                    }
                    
                    // 提交表单以保持下拉框状态
                    form.submit();
                };
            }
            
            // 确保计算后保持下拉框状态
            var calculateBtn = document.querySelector('button[name="calculate"]');
            if (calculateBtn && extraVisibleInput && extraContent) {
                calculateBtn.addEventListener('click', function() {
                    extraVisibleInput.value = extraContent.style.display === 'block' ? '1' 
: '0';
                });
            }
        });
    </script>
    <!-- 音乐播放器所用的文件 -->
<!-- 引入 APlayer 样式表 -->
<link rel="stylesheet" href="https://npm.elemecdn.com/aplayer@1.10.1/dist/APlayer.min.css">
<!-- 引入 APlayer JavaScript -->
<script src="https://npm.elemecdn.com/aplayer@1.10.1/dist/APlayer.min.js"></script>

<!-- 引入 MetingJS，用于加载网易云音乐歌单 -->
<script src="https://npm.elemecdn.com/meting@2.0.1/dist/Meting.min.js"></script>

<!-- 自定义播放器容器，设置为可见 -->
<div id="customize" style="position: fixed; bottom: 0; left: 0; width: 100%; z-index: 9999;">
    <meting-js
        fixed="true"         
        autoplay="false"     
        theme="#409EFF"    
        list-folded="true"   
        auto="https://music.163.com/m/playlist?id=5464031258&creatorId=4043396459"></meting-js>
</div>
</body>
</html>