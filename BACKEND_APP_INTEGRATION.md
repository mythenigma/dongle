# App 与 PHP 后端对接说明

## 一、整体流程（与网页一致）

```
[App] 选文件 → POST r2upload.php (multipart file) → 返回 path
[App] 填表单 → POST maipdf2026_backend.php (sender=path + 各项参数) → 返回 HTML
[App] 解析 HTML 中的链接、Read Code、Modify Code → 展示 + 生成 QR
```

---

## 二、上传接口 r2upload.php

### App 已按以下约定请求

| 项目 | 说明 |
|------|------|
| URL | `https://maipdf.com/pdf/r2upload.php` |
| 方法 | POST，`multipart/form-data` |
| 参数 | `file`：单文件（PDF），与网页 Dropzone 的 `paramName: "file"` 一致 |

### 服务器端（你已有）

- 接收 `$_FILES["file"]`，返回 **JSON**。
- 成功示例：`{"status":"success","mode":"standard","file":"xxx.pdf","path":"/2026/03/04/xxx.pdf","reused_existing":false}`。
- App 用返回里的 **`path`** 作为 Step 2 的 `sender` 提交给 backend。

### 注意

- 当前 `r2upload.php` 只允许扩展名 `pdf`、`htm`（第 133 行）。App 端已限制只选 **PDF**，与后端一致。
- 若以后要支持图片上传，需在 PHP 中把 `$allowedExts` 加上 `jpg`、`jpeg`、`png`、`gif`，并确认后续逻辑（预览、加密等）支持这些类型。

---

## 三、生成链接接口 maipdf2026_backend.php

### App 已发送的 POST 字段（与网页表单对齐）

| 字段 | 说明 | 示例 |
|------|------|------|
| sender | 上传返回的 path | `/2026/03/04/xxx.pdf` |
| file_id | 可选，上传返回的 file_id（若 PHP 有返回） | — |
| limit | 打开次数限制 | 1–99999999 |
| password | 单次阅读时长（秒） | ≥30 |
| darkmode | 动态水印 | `yes` 或空 |
| zhangai | 查看类型 | `straight` / `obstacle` / `topen` |
| expiration_ts | 过期时间（Unix 秒），0 表示永久 | 数字或空 |
| expiration_day | 自定义天数（仅 custom 时用） | 数字 |
| expiration_preset | 预设（1h/3h/24h/5d/custom/unlimited） | 仅网页用，App 已换算成 expiration_ts |
| enableTelegramAlert | 是否启用 Telegram 提醒 | `yes` 或空 |
| mailalert | Telegram chat_id 等 | 字符串 |
| enableEmailValidation | 是否邮箱验证 | `yes` 或空 |
| emailAddresses | 邮箱列表，逗号分隔 | 字符串 |

### 服务器端必须做的修改：允许来自 App 的请求（绕过 Cookie 检查）

网页在「上传成功后」会设置 Cookie：`document.cookie = "uploadedfile=success"`，所以同一浏览器再提交表单时带有该 Cookie。  
**App 不会带这个 Cookie**，因此当前 PHP 里这段会拦截所有 App 请求：

```php
if($_COOKIE["uploadedfile"] == "notyet") {
    exit("Please do not refresh...");
}
```

**做法：对来自 App 的请求跳过 Cookie 检查。**

在 **maipdf2026_backend.php** 中，找到上述 `if($_COOKIE["uploadedfile"] == "notyet")` 判断，改成：

```php
// 来自 Flutter App 的请求不带网页 Cookie，通过头标识放行
$isAppRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && (strpos(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'flutter') !== false);

if (!$isAppRequest && isset($_COOKIE["uploadedfile"]) && $_COOKIE["uploadedfile"] == "notyet") {
    exit("<script>
        document.getElementById(\"2step\").className=\"text-danger\";
        document.getElementById(\"2step\").innerHTML = 
        \"Please do not refresh the page<br>Reopen it instead\";
        
        document.getElementById(\"2step3\").innerHTML = 
        \"Please do not refresh the page<br>Reopen it instead\";
        </script>");
}
```

即：**只有「网页请求且 uploadedfile == notyet」才拦截**；带 `X-Requested-With: FlutterApp`（或含 flutter）的请求不再检查 Cookie。

### 后端返回格式（保持不变即可）

- 仍返回 **HTML**，其中包含：
  - `<input id="myInput" value="完整链接">`
  - `id="result-message"`（Read Code 相关）
  - `id="result-password"`（Modify Code 相关）
- App 用正则从这段 HTML 里解析出链接和两个 code，无需你改 HTML 结构。

---

## 四、Flutter 端已做的对齐

1. **上传**：只选 PDF，POST `file` 到 `r2upload.php`，解析 JSON 的 `path`（及可选 `file_id`）。
2. **生成链接**：POST 所有表单字段到 `maipdf2026_backend.php`，并增加请求头 **`X-Requested-With: FlutterApp`**，以便 PHP 识别并跳过 Cookie 检查。
3. **过期时间**：按预设（1h/3h/24h/5d/custom/unlimited）在 App 内换算成 **expiration_ts** 再提交，与网页 JS 逻辑一致。
4. **校验**：`limit`、`password` 在 App 里做了与后端一致的最小值（limit≥1，password≥30），避免后端直接 exit。

---

## 五、可选：为 App 单独提供 JSON 接口

若你希望后端对 App 返回 **JSON** 而不是 HTML，可以：

- 在 `maipdf2026_backend.php` 里根据 `X-Requested-With: FlutterApp` 判断：
  - 若是 App：在生成完链接后只输出一段 JSON，例如：
    - `link_full`, `link_short`, `read_code`, `modify_code`
  - 若是网页：保持现有 HTML 输出不变。

这样 App 就不必解析 HTML，直接解析 JSON 即可。当前实现已能基于现有 HTML 工作，这一条是可选的优化。

---

## 六、R2 / 域名

- 上传和生成链接都只请求 **你的域名**（如 `https://maipdf.com/pdf/...`）。
- R2 的绑定域名、鉴权、路径等全部在 **你现有的 PHP 和 Worker 里** 完成，App 不直接访问 R2，也无需在 App 里配置任何 R2 或域名。

---

## 七、小结

| 端 | 要做的事 |
|----|----------|
| **PHP** | 在 `maipdf2026_backend.php` 中按上文增加 `$isAppRequest` 判断，对 App 请求跳过 `uploadedfile` Cookie 检查。 |
| **Flutter** | 已按上述字段和头对接；如需，可再根据你的 JSON 接口做小改动。 |

按上述改好后，App 的上传 → 生成链接 → 展示链接/二维码 会与网页共用同一套后端逻辑并正确打通。
