package com.kalpkrut.pathshala

import android.annotation.SuppressLint
import android.app.Activity
import android.content.ActivityNotFoundException
import android.content.Intent
import android.graphics.Bitmap
import android.graphics.Color
import android.net.ConnectivityManager
import android.net.NetworkCapabilities
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.webkit.CookieManager
import android.webkit.DownloadListener
import android.webkit.URLUtil
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.FrameLayout
import android.widget.ProgressBar
import android.widget.Toast
import androidx.activity.OnBackPressedCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.WindowCompat
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout

/**
 * Kalpkrut Pathshala — native WebView shell for the live ERP.
 */
class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private lateinit var swipeRefresh: SwipeRefreshLayout
    private lateinit var progressBar: ProgressBar
    private lateinit var loadingOverlay: FrameLayout

    private val appUrl = "https://pathshala.kalpkrutinfotech.com"

    private var filePathCallback: ValueCallback<Array<Uri>>? = null

    private val filePickerLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult()
    ) { result ->
        val callback = filePathCallback
        filePathCallback = null
        if (callback == null) return@registerForActivityResult

        if (result.resultCode == Activity.RESULT_OK) {
            val intentData = result.data
            val results: Array<Uri>? = when {
                intentData?.data != null -> arrayOf(intentData.data!!)
                intentData?.clipData != null -> {
                    val clipData = intentData.clipData!!
                    Array(clipData.itemCount) { i -> clipData.getItemAt(i).uri }
                }
                else -> null
            }
            callback.onReceiveValue(results)
        } else {
            callback.onReceiveValue(null)
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        WindowCompat.setDecorFitsSystemWindows(window, true)
        setContentView(R.layout.activity_main)

        webView = findViewById(R.id.webView)
        swipeRefresh = findViewById(R.id.swipeRefresh)
        progressBar = findViewById(R.id.progressBar)
        loadingOverlay = findViewById(R.id.loadingOverlay)

        setupWebView()

        swipeRefresh.setColorSchemeColors(getColor(R.color.brand_primary))
        swipeRefresh.setOnRefreshListener { reloadSafely() }

        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                when {
                    webView.canGoBack() -> webView.goBack()
                    else -> finish()
                }
            }
        })

        if (savedInstanceState != null) {
            webView.restoreState(savedInstanceState)
        } else {
            openApp()
        }
    }

    override fun onSaveInstanceState(outState: Bundle) {
        super.onSaveInstanceState(outState)
        webView.saveState(outState)
    }

    override fun onPause() {
        webView.onPause()
        super.onPause()
    }

    override fun onResume() {
        super.onResume()
        webView.onResume()
    }

    override fun onDestroy() {
        swipeRefresh.removeAllViews()
        webView.stopLoading()
        webView.destroy()
        super.onDestroy()
    }

    private fun openApp() {
        if (!isOnline()) {
            showOfflinePage()
            return
        }
        showLoading(true)
        webView.loadUrl(appUrl)
    }

    private fun reloadSafely() {
        if (!isOnline()) {
            swipeRefresh.isRefreshing = false
            showOfflinePage()
            return
        }
        showLoading(true)
        webView.reload()
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun setupWebView() {
        CookieManager.getInstance().setAcceptCookie(true)
        CookieManager.getInstance().setAcceptThirdPartyCookies(webView, true)

        val settings = webView.settings
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.databaseEnabled = true
        settings.useWideViewPort = true
        settings.loadWithOverviewMode = true
        settings.allowFileAccess = true
        settings.allowContentAccess = true
        settings.setSupportZoom(false)
        settings.builtInZoomControls = false
        settings.displayZoomControls = false
        settings.cacheMode = WebSettings.LOAD_DEFAULT
        settings.mediaPlaybackRequiresUserGesture = false
        settings.mixedContentMode = WebSettings.MIXED_CONTENT_COMPATIBILITY_MODE
        settings.userAgentString = settings.userAgentString + " KalpkrutPathshalaApp/1.0.0"
        WebView.setWebContentsDebuggingEnabled(false)

        webView.setBackgroundColor(Color.WHITE)
        webView.setLayerType(View.LAYER_TYPE_HARDWARE, null)

        webView.webViewClient = object : WebViewClient() {
            override fun onPageStarted(view: WebView?, url: String?, favicon: Bitmap?) {
                progressBar.visibility = View.VISIBLE
                progressBar.progress = 0
            }

            override fun onPageFinished(view: WebView?, url: String?) {
                progressBar.visibility = View.GONE
                swipeRefresh.isRefreshing = false
                showLoading(false)
            }

            override fun onReceivedError(
                view: WebView?,
                request: WebResourceRequest?,
                error: WebResourceError?
            ) {
                if (request?.isForMainFrame == true) {
                    swipeRefresh.isRefreshing = false
                    progressBar.visibility = View.GONE
                    showLoading(false)
                    showOfflinePage()
                }
            }

            override fun shouldOverrideUrlLoading(
                view: WebView?,
                request: WebResourceRequest?
            ): Boolean {
                val url = request?.url?.toString() ?: return false
                return handleExternalUrl(url)
            }
        }

        webView.webChromeClient = object : WebChromeClient() {
            override fun onProgressChanged(view: WebView?, newProgress: Int) {
                progressBar.progress = newProgress
                progressBar.visibility = if (newProgress in 1..99) View.VISIBLE else View.GONE
                if (newProgress >= 90) {
                    showLoading(false)
                }
            }

            override fun onShowFileChooser(
                webView: WebView?,
                filePathCallback: ValueCallback<Array<Uri>>?,
                fileChooserParams: FileChooserParams?
            ): Boolean {
                this@MainActivity.filePathCallback?.onReceiveValue(null)
                this@MainActivity.filePathCallback = filePathCallback

                val intent = fileChooserParams?.createIntent()
                    ?: Intent(Intent.ACTION_GET_CONTENT).apply {
                        type = "*/*"
                        addCategory(Intent.CATEGORY_OPENABLE)
                        putExtra(Intent.EXTRA_ALLOW_MULTIPLE, true)
                    }

                return try {
                    filePickerLauncher.launch(intent)
                    true
                } catch (_: Exception) {
                    this@MainActivity.filePathCallback = null
                    false
                }
            }
        }

        webView.setDownloadListener(DownloadListener { url, _, contentDisposition, mimeType, _ ->
            try {
                val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                startActivity(intent)
                Toast.makeText(
                    this,
                    "Opening download: ${URLUtil.guessFileName(url, contentDisposition, mimeType)}",
                    Toast.LENGTH_SHORT
                ).show()
            } catch (_: ActivityNotFoundException) {
                Toast.makeText(this, "No app available to open download", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun handleExternalUrl(url: String): Boolean {
        val lower = url.lowercase()
        if (
            lower.startsWith("tel:") ||
            lower.startsWith("mailto:") ||
            lower.startsWith("whatsapp:") ||
            lower.startsWith("https://wa.me/") ||
            lower.startsWith("intent:")
        ) {
            return try {
                startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(url)))
                true
            } catch (_: Exception) {
                Toast.makeText(this, "App not available to handle link", Toast.LENGTH_SHORT).show()
                true
            }
        }

        // Keep ERP navigation inside the WebView.
        if (lower.startsWith("https://pathshala.kalpkrutinfotech.com") ||
            lower.startsWith("http://pathshala.kalpkrutinfotech.com")
        ) {
            return false
        }

        return try {
            startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(url)))
            true
        } catch (_: Exception) {
            false
        }
    }

    private fun showLoading(show: Boolean) {
        loadingOverlay.visibility = if (show) View.VISIBLE else View.GONE
    }

    private fun isOnline(): Boolean {
        val cm = getSystemService(ConnectivityManager::class.java) ?: return true
        val network = cm.activeNetwork ?: return false
        val caps = cm.getNetworkCapabilities(network) ?: return false
        return caps.hasCapability(NetworkCapabilities.NET_CAPABILITY_INTERNET)
    }

    private fun showOfflinePage() {
        showLoading(false)
        progressBar.visibility = View.GONE
        swipeRefresh.isRefreshing = false
        val html = """
            <!DOCTYPE html>
            <html>
            <head>
              <meta name="viewport" content="width=device-width, initial-scale=1">
              <style>
                body{font-family:sans-serif;background:#0f172a;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:24px;text-align:center}
                .card{background:#1e293b;border-radius:16px;padding:28px;max-width:360px}
                h1{font-size:20px;margin:0 0 8px}
                p{color:#94a3b8;font-size:14px;line-height:1.5}
                a{display:inline-block;margin-top:16px;background:#4f46e5;color:#fff;text-decoration:none;padding:12px 20px;border-radius:10px;font-weight:600}
              </style>
            </head>
            <body>
              <div class="card">
                <h1>Connection problem</h1>
                <p>Pathshala needs internet to load your institute data. Check Wi‑Fi/mobile data and retry.</p>
                <a href="$appUrl">Retry</a>
              </div>
            </body>
            </html>
        """.trimIndent()
        webView.loadDataWithBaseURL(appUrl, html, "text/html", "UTF-8", null)
    }
}
