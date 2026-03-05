# App 内链接与文件结构说明

## 一、当前实现的链接

| 来源 | 目标 | 在 App 中的处理 |
|------|------|-----------------|
| 首页「Online MaiPDF Cloud Sharing」→ START | `pdf/maipdf2026.html` | **已实现**：点击后打开 `CloudSharePage`，用 WebView 加载 `https://maipdf.com/pdf/maipdf2026.html` |
| 其它链接（Blog、Text PDF to Image、DRM、QR、Access Record、PRIVNOTE 等） | 各自外部/站内 URL | **未做**：按你的要求只做了上述这一条，其它链接没有在 App 里接。 |

也就是说：**只有「Online MaiPDF Cloud Sharing」→ pdf/maipdf2026.html 这条链路在 App 里接好了**，其它链接保持不动。

---

## 二、App 内「链接」是怎么做的

- **不是**在 App 里放一个 `pdf/maipdf2026.html` 文件再点开。
- **是**：首页一个 Flutter 按钮/卡片 → 跳转到新页面 → 新页面里用 **WebView 加载线上地址**  
  `https://maipdf.com/pdf/maipdf2026.html`。

流程简述：

1. 用户点首页「START」→ `Navigator.push` 到 `CloudSharePage`。
2. `CloudSharePage` 里用 `WebViewController.loadRequest(Uri.parse(url))` 打开该 URL。
3. 所以**链接**在代码里就是一个**字符串 URL**，不存在「App 内的 html 文件路径」这种路由。

以后如果要加更多「入口」：

- 再写一个 Flutter 页面（或复用一个「通用 WebView 页」），传入不同 URL 即可。
- 如需在 WebView 内拦截某些链接（例如站内跳转），可在 `NavigationDelegate` 里处理。

---

## 三、项目里和「网页/PDF」有关的文件结构

- **`maipdf_htmls/`**  
  - 你放在项目里的**网页源码/参考**，例如 `index.html`、`pdf/maipdf2026.html` 等。  
  - **不会**被打包进 App，也**不会**被 Flutter 当作路由或资源加载。  
  - 用途：给你和 AI 看结构、改版、对照用；和 App 运行时的「链接」无直接关系。

- **`lib/`**  
  - App 真正用到的代码：
    - `lib/main.dart`：入口，直接打开首页 `MaipdfHomePage`。
    - `lib/screens/maipdf_home_page.dart`：首页，只有「Online MaiPDF Cloud Sharing」一张卡片，点 START 打开 Cloud Sharing 页。
    - `lib/screens/cloud_share_page.dart`：内嵌 WebView 加载 `https://maipdf.com/pdf/maipdf2026.html`。

- **线上 URL 与本地 `maipdf_htmls` 的关系**  
  - App 只访问 **线上**：`https://maipdf.com/pdf/maipdf2026.html`。  
  - 本地 `maipdf_htmls/pdf/maipdf2026.html` 仅作源码参考，不参与「链接」逻辑。

---

## 四、如果以后要把某页做成「包在 App 里的本地 HTML」

如果将来你希望某页（例如一个说明页）不依赖网络，用 App 内自带的 HTML：

1. **放文件**  
   - 在项目里建目录，例如 `assets/html/`。  
   - 把对应的 `.html`（以及用到的 css/js 等）放进去。

2. **在 pubspec.yaml 里声明资源**  
   ```yaml
   flutter:
     assets:
       - assets/html/
   ```

3. **在 Flutter 里加载**  
   - 用 `rootBundle.loadString('assets/html/xxx.html')` 读成字符串；  
   - 再用 WebView 的 `loadHtmlString(htmlString, baseUrl: ...)` 显示。  
   - 或使用 `loadRequest(Uri.parse('file:///...'))` 等方式（视平台与需求而定）。

当前实现**没有**把 `maipdf_htmls/` 当作 assets 使用，所以**没有**做上述步骤；链接只指向线上 URL。

---

## 五、小结

- **唯一在 App 里接好的链接**：首页「Online MaiPDF Cloud Sharing」→ 打开 `https://maipdf.com/pdf/maipdf2026.html`（WebView）。  
- **其它链接**：未做，保持原样。  
- **链接在 App 中的实现方式**：Flutter 页面 + WebView + 传入 URL 字符串。  
- **文件结构**：`maipdf_htmls/` 仅作源码参考；实际运行只用 `lib/` 和线上地址。
