# カスタムCSS/JS per Post プラグイン

投稿記事ごとにカスタムのCSS、JavaScriptを追加できるWordPressプラグインです。

## 特徴

- 📝 投稿・固定ページごとに個別のCSSを追加
- 💻 投稿・固定ページごとに個別のJavaScriptを追加
- 🔒 セキュリティ対策済み（Nonce認証、サニタイゼーション）
- 🎨 管理画面で簡単に編集可能
- ⚡ フロントエンドで自動出力

## インストール方法

1. `custom-css-js-per-post.php` をWordPressの `wp-content/plugins/` ディレクトリにアップロード
2. WordPress管理画面の「プラグイン」メニューからプラグインを有効化

## 使い方

### 1. 投稿・固定ページの編集画面

プラグインを有効化すると、投稿・固定ページの編集画面に以下のメタボックスが追加されます：

- **カスタムCSS** - この投稿だけに適用されるCSSを記述
- **カスタムJavaScript** - この投稿だけに適用されるJSを記述

### 2. CSSの追加例

```css
.my-custom-class {
    color: #ff0000;
    font-size: 18px;
    font-weight: bold;
}

h2 {
    border-left: 4px solid #3498db;
    padding-left: 10px;
}
```

**注意**: `<style>` タグは不要です。

### 3. JavaScriptの追加例

```javascript
jQuery(document).ready(function($) {
    console.log('カスタムJS実行');
    
    // 特定の要素にクリックイベントを追加
    $('.my-button').on('click', function() {
        alert('ボタンがクリックされました！');
    });
});
```

**注意**: `<script>` タグは不要です。

## 出力される場所

- **CSS**: `<head>` タグ内の最後に出力されます
- **JavaScript**: `</body>` タグの直前に出力されます

## セキュリティ

- Nonce認証による保存処理の検証
- 権限チェック（編集権限のあるユーザーのみ）
- HTMLタグのサニタイゼーション
- エスケープ処理

## 対応投稿タイプ

- 投稿（post）
- 固定ページ（page）

## システム要件

- WordPress 5.0以上
- PHP 7.0以上

## よくある質問

### Q: CSSやJSが反映されません

A: ブラウザのキャッシュをクリアしてください。また、キャッシュプラグインを使用している場合は、そちらのキャッシュもクリアしてください。

### Q: カスタム投稿タイプにも対応できますか？

A: はい。`ccjpp_add_meta_boxes` 関数内の `$screens` 配列にカスタム投稿タイプのスラッグを追加してください。

```php
$screens = array( 'post', 'page', 'your_custom_post_type' );
```

### Q: 管理者以外のユーザーにも使わせたいのですが？

A: デフォルトでは投稿の編集権限があれば使用できます。さらに制限したい場合は、`ccjpp_save_meta_boxes` 関数の権限チェック部分をカスタマイズしてください。

## ライセンス

GPL v2 or later

## 更新履歴

### 1.0.0
- 初回リリース
- カスタムCSS機能
- カスタムJavaScript機能
