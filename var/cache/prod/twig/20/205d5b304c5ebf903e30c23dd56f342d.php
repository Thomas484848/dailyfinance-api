<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* @ApiPlatform/SwaggerUi/index.html.twig */
class __TwigTemplate_492428051afbb3420a8716b46c43f186 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'head_metas' => [$this, 'block_head_metas'],
            'title' => [$this, 'block_title'],
            'stylesheet' => [$this, 'block_stylesheet'],
            'head_javascript' => [$this, 'block_head_javascript'],
            'header' => [$this, 'block_header'],
            'javascript' => [$this, 'block_javascript'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 2
        yield "<!DOCTYPE html>
<html>
<head>
    ";
        // line 5
        yield from $this->unwrap()->yieldBlock('head_metas', $context, $blocks);
        // line 8
        yield "
    ";
        // line 9
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        // line 12
        yield "
    ";
        // line 13
        yield from $this->unwrap()->yieldBlock('stylesheet', $context, $blocks);
        // line 19
        yield "
    ";
        // line 20
        $context["oauth_data"] = ["oauth" => Twig\Extension\CoreExtension::merge(CoreExtension::getAttribute($this->env, $this->source, ($context["swagger_data"] ?? null), "oauth", [], "any", false, false, false, 20), ["redirectUrl" => $this->extensions['Symfony\Bridge\Twig\Extension\HttpFoundationExtension']->generateAbsoluteUrl($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/swagger-ui/oauth2-redirect.html", ($context["assetPackage"] ?? null)))])];
        // line 21
        yield "
    ";
        // line 22
        yield from $this->unwrap()->yieldBlock('head_javascript', $context, $blocks);
        // line 26
        yield "</head>

<body>
<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"svg-icons\">
    <defs>
        <symbol viewBox=\"0 0 20 20\" id=\"unlocked\">
            <path d=\"M15.8 8H14V5.6C14 2.703 12.665 1 10 1 7.334 1 6 2.703 6 5.6V6h2v-.801C8 3.754 8.797 3 10 3c1.203 0 2 .754 2 2.199V8H4c-.553 0-1 .646-1 1.199V17c0 .549.428 1.139.951 1.307l1.197.387C5.672 18.861 6.55 19 7.1 19h5.8c.549 0 1.428-.139 1.951-.307l1.196-.387c.524-.167.953-.757.953-1.306V9.199C17 8.646 16.352 8 15.8 8z\"></path>
        </symbol>

        <symbol viewBox=\"0 0 20 20\" id=\"locked\">
            <path d=\"M15.8 8H14V5.6C14 2.703 12.665 1 10 1 7.334 1 6 2.703 6 5.6V8H4c-.553 0-1 .646-1 1.199V17c0 .549.428 1.139.951 1.307l1.197.387C5.672 18.861 6.55 19 7.1 19h5.8c.549 0 1.428-.139 1.951-.307l1.196-.387c.524-.167.953-.757.953-1.306V9.199C17 8.646 16.352 8 15.8 8zM12 8H8V5.199C8 3.754 8.797 3 10 3c1.203 0 2 .754 2 2.199V8z\"></path>
        </symbol>

        <symbol viewBox=\"0 0 20 20\" id=\"close\">
            <path d=\"M14.348 14.849c-.469.469-1.229.469-1.697 0L10 11.819l-2.651 3.029c-.469.469-1.229.469-1.697 0-.469-.469-.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-.469-.469-.469-1.228 0-1.697.469-.469 1.228-.469 1.697 0L10 8.183l2.651-3.031c.469-.469 1.228-.469 1.697 0 .469.469.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c.469.469.469 1.229 0 1.698z\"></path>
        </symbol>

        <symbol viewBox=\"0 0 20 20\" id=\"large-arrow\">
            <path d=\"M13.25 10L6.109 2.58c-.268-.27-.268-.707 0-.979.268-.27.701-.27.969 0l7.83 7.908c.268.271.268.709 0 .979l-7.83 7.908c-.268.271-.701.27-.969 0-.268-.269-.268-.707 0-.979L13.25 10z\"></path>
        </symbol>

        <symbol viewBox=\"0 0 20 20\" id=\"large-arrow-down\">
            <path d=\"M17.418 6.109c.272-.268.709-.268.979 0s.271.701 0 .969l-7.908 7.83c-.27.268-.707.268-.979 0l-7.908-7.83c-.27-.268-.27-.701 0-.969.271-.268.709-.268.979 0L10 13.25l7.418-7.141z\"></path>
        </symbol>

        <symbol viewBox=\"0 0 24 24\" id=\"jump-to\">
            <path d=\"M19 7v4H5.83l3.58-3.59L8 6l-6 6 6 6 1.41-1.41L5.83 13H21V7z\"></path>
        </symbol>

        <symbol viewBox=\"0 0 24 24\" id=\"expand\">
            <path d=\"M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z\"></path>
        </symbol>
    </defs>
</svg>

";
        // line 61
        yield from $this->unwrap()->yieldBlock('header', $context, $blocks);
        // line 68
        yield "
";
        // line 69
        if ((($tmp = ($context["showWebby"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 70
            yield "    <div class=\"web\"><img src=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/web.png", ($context["assetPackage"] ?? null)), "html", null, true);
            yield "\"></div>
    <div class=\"webby\"><img src=\"";
            // line 71
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/webby.png", ($context["assetPackage"] ?? null)), "html", null, true);
            yield "\"></div>
";
        }
        // line 73
        yield "
<div id=\"swagger-ui\" class=\"api-platform\"></div>

<div class=\"swagger-ui\" id=\"formats\">
    <div class=\"information-container wrapper\">
        <div class=\"info\">
            Available formats:
            ";
        // line 80
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(Twig\Extension\CoreExtension::keys(($context["formats"] ?? null)));
        foreach ($context['_seq'] as $context["_key"] => $context["format"]) {
            // line 81
            yield "                <a href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(($context["originalRoute"] ?? null), Twig\Extension\CoreExtension::merge(($context["originalRouteParams"] ?? null), ["_format" => $context["format"]])), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["format"], "html", null, true);
            yield "</a>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['format'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 83
        yield "            <br>
            Other API docs:
            ";
        // line 85
        $context["active_ui"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["app"] ?? null), "request", [], "any", false, false, false, 85), "query", [], "any", false, false, false, 85), "get", ["ui", "swagger_ui"], "method", false, false, false, 85);
        // line 86
        yield "            ";
        if ((($context["swaggerUiEnabled"] ?? null) && (($context["active_ui"] ?? null) != "swagger_ui"))) {
            yield "<a href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(($context["originalRoute"] ?? null), ($context["originalRouteParams"] ?? null)), "html", null, true);
            yield "\">Swagger UI</a>";
        }
        // line 87
        yield "            ";
        if ((($context["reDocEnabled"] ?? null) && (($context["active_ui"] ?? null) != "re_doc"))) {
            yield "<a href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(($context["originalRoute"] ?? null), Twig\Extension\CoreExtension::merge(($context["originalRouteParams"] ?? null), ["ui" => "re_doc"])), "html", null, true);
            yield "\">ReDoc</a>";
        }
        // line 88
        yield "            ";
        if (( !($context["graphQlEnabled"] ?? null) || ($context["graphiQlEnabled"] ?? null))) {
            yield "<a ";
            if ((($tmp = ($context["graphiQlEnabled"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                yield "href=\"";
                yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("api_graphql_graphiql");
                yield "\"";
            }
            yield " class=\"graphiql-link\">GraphiQL</a>";
        }
        // line 89
        yield "        </div>
    </div>
</div>

";
        // line 93
        yield from $this->unwrap()->yieldBlock('javascript', $context, $blocks);
        // line 330
        yield "
</body>
</html>
";
        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_head_metas(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 6
        yield "        <meta charset=\"UTF-8\">
    ";
        yield from [];
    }

    // line 9
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 10
        yield "        <title>";
        if ((($tmp = ($context["title"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["title"] ?? null), "html", null, true);
            yield " - ";
        }
        yield "API Platform</title>
    ";
        yield from [];
    }

    // line 13
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_stylesheet(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 14
        yield "        <link rel=\"stylesheet\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/fonts/open-sans/400.css", ($context["assetPackage"] ?? null)), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/fonts/open-sans/700.css", ($context["assetPackage"] ?? null)), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 16
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/swagger-ui/swagger-ui.css", ($context["assetPackage"] ?? null)), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 17
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/style.css", ($context["assetPackage"] ?? null)), "html", null, true);
        yield "\">
    ";
        yield from [];
    }

    // line 22
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_head_javascript(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 23
        yield "        ";
        // line 24
        yield "        <script id=\"swagger-data\" type=\"application/json\">";
        yield json_encode(Twig\Extension\CoreExtension::merge(($context["swagger_data"] ?? null), ($context["oauth_data"] ?? null)), 65);
        yield "</script>
    ";
        yield from [];
    }

    // line 61
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_header(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 62
        yield "    <header>
        <a id=\"logo\" href=\"https://api-platform.com\">
            <img src=\"";
        // line 64
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/logo-header.svg", ($context["assetPackage"] ?? null)), "html", null, true);
        yield "\" alt=\"API Platform\">
        </a>
    </header>
";
        yield from [];
    }

    // line 93
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascript(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 94
        if (((($context["reDocEnabled"] ?? null) &&  !($context["swaggerUiEnabled"] ?? null)) || (($context["reDocEnabled"] ?? null) && ("re_doc" == ($context["active_ui"] ?? null))))) {
            // line 95
            yield "    <script src=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/redoc/redoc.standalone.js", ($context["assetPackage"] ?? null)), "html", null, true);
            yield "\"></script>
    <script src=\"";
            // line 96
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/init-redoc-ui.js", ($context["assetPackage"] ?? null)), "html", null, true);
            yield "\"></script>
";
        } else {
            // line 98
            yield "    <script src=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/swagger-ui/swagger-ui-bundle.js", ($context["assetPackage"] ?? null)), "html", null, true);
            yield "\"></script>
    <script src=\"";
            // line 99
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/swagger-ui/swagger-ui-standalone-preset.js", ($context["assetPackage"] ?? null)), "html", null, true);
            yield "\"></script>

    ";
            // line 102
            yield "    <script>
      (function () {
        const DEBUG = false;
        const log = DEBUG ? console.log.bind(console, \"### [APIP]\") : function(){};
        const warn = DEBUG ? console.warn.bind(console, \"### [APIP]\") : function(){};

        const TOKEN_KEY = \"jwt_token\";
        const LOGIN_RE = /\\/api\\/login(\\?|\$)/;

        function getToken() {
          try { return sessionStorage.getItem(TOKEN_KEY); }
          catch (e) { return null; }
        }
        function setToken(token) {
          try { sessionStorage.setItem(TOKEN_KEY, token); } catch (e) {}
        }

        function waitFor(predicate, maxMs, label) {
          const start = Date.now();
          return new Promise(resolve => {
            (function tick() {
              let ok = false;
              try { ok = !!predicate(); } catch (e) {}
              if (ok) return resolve(true);
              if (Date.now() - start > maxMs) {
                warn(\"waitFor TIMEOUT:\", label);
                return resolve(false);
              }
              setTimeout(tick, 100);
            })();
          });
        }

        function getSystemFromUi(ui) {
          try {
            if (!ui) return null;
            if (typeof ui.getSystem === \"function\") return ui.getSystem();
            if (ui.system) return ui.system;
            return null;
          } catch (e) {
            return null;
          }
        }

        function hookSwaggerUiBundle() {
          if (window.__apipHookedFactory) return true;

          const factoryName = (typeof window.SwaggerUIBundle === \"function\") ? \"SwaggerUIBundle\"
                            : (typeof window.SwaggerUI === \"function\") ? \"SwaggerUI\"
                            : null;

          if (!factoryName) return false;

          const original = window[factoryName];
          if (typeof original !== \"function\") return false;

          window.__apipHookedFactory = true;

          window[factoryName] = function (config) {
            // Force disable persistAuthorization (évite les soucis cookie/persist internes)
            config = config || {};
            config.persistAuthorization = false;

            // ⚠️ Important : passer CONFIG MODIFIÉ, pas \"arguments\"
            const ui = original.call(this, config);

            window.__apipUi = ui;
            window.__apipSystem = getSystemFromUi(ui);

            // tente authorize si token déjà là
            applyAuthorizeWhenReady(\"captured-ui\");
            return ui;
          };

          // copy static props
          for (const k in original) {
            try { window[factoryName][k] = original[k]; } catch(e) {}
          }

          return true;
        }

        async function getSystem() {
          if (window.__apipSystem) return window.__apipSystem;

          const okFactory = await waitFor(
            () => typeof window.SwaggerUIBundle === \"function\" || typeof window.SwaggerUI === \"function\",
            10000,
            \"SwaggerUIBundle/SwaggerUI factory\"
          );
          if (!okFactory) return null;

          hookSwaggerUiBundle();

          const okUi = await waitFor(() => !!window.__apipUi, 10000, \"__apipUi instance\");
          if (!okUi) return null;

          window.__apipSystem = getSystemFromUi(window.__apipUi);

          const okSystem = await waitFor(() => !!getSystemFromUi(window.__apipUi), 5000, \"ui system\");
          if (!okSystem) return null;

          window.__apipSystem = getSystemFromUi(window.__apipUi);
          return window.__apipSystem;
        }

        async function applyAuthorizeWhenReady(reason) {
          const tokenRaw = getToken();
          if (!tokenRaw) return false;

          const token = tokenRaw.startsWith(\"Bearer \") ? tokenRaw.slice(7) : tokenRaw;

          const system = await getSystem();
          if (!system) return false;

          const okSchemes = await waitFor(() => {
            try {
              const spec = system.specSelectors.specJson().toJS();
              const schemes = spec && spec.components && spec.components.securitySchemes;
              return schemes && Object.keys(schemes).length > 0;
            } catch (e) { return false; }
          }, 10000, \"securitySchemes\");

          if (!okSchemes) return false;

          const spec = system.specSelectors.specJson().toJS();
          const schemes = spec.components.securitySchemes;

          // ✅ Chez toi le scheme s'appelle \"JWT\"
          const schemeName = schemes.JWT ? \"JWT\" : Object.keys(schemes)[0];
          const scheme = schemes[schemeName];

          if (!system.authActions || !system.authActions.authorize) return false;

          // Evite de spam authorize 15 fois
          if (window.__apipAuthorizedOnce) return true;
          window.__apipAuthorizedOnce = true;

          // Swagger UI: pour http/bearer -> value doit être le TOKEN (sans \"Bearer \")
          const isHttpBearer = scheme && scheme.type === \"http\" && String(scheme.scheme || \"\").toLowerCase() === \"bearer\";

          const authPayload = {
            [schemeName]: {
              name: schemeName,
              schema: scheme,
              value: isHttpBearer ? token : (\"Bearer \" + token)
            }
          };

          try {
            system.authActions.authorize(authPayload);
          } catch (e) {
            // si jamais ça throw, on autorise une nouvelle tentative plus tard
            window.__apipAuthorizedOnce = false;
            return false;
          }

          return true;
        }

        function wrapFetchOnce() {
          if (window.__apipFetchWrapped) return;
          window.__apipFetchWrapped = true;

          const originalFetch = window.fetch;

          window.fetch = async function (input, init) {
            let url = \"\";
            try { url = (typeof input === \"string\") ? input : (input && input.url) ? input.url : \"\"; } catch (e) {}

            init = init || {};
            init.headers = init.headers || {};

            const tokenRaw = getToken();
            const isLogin = LOGIN_RE.test(url);

            // Ajoute Authorization sur toutes les routes sauf /api/login
            if (tokenRaw && !isLogin) {
              const token = tokenRaw.startsWith(\"Bearer \") ? tokenRaw.slice(7) : tokenRaw;
              init.headers[\"Authorization\"] = \"Bearer \" + token;
            }

            const res = await originalFetch(input, init);

            // Capture token si /api/login renvoie du JSON {token: ...}
            // (Chez toi /api/login renvoie 204 + Set-Cookie => ici ça ne déclenchera pas)
            try {
              if (isLogin && res && res.ok) {
                const clone = res.clone();
                const data = await clone.json().catch(() => null);

                const t = data && (data.token || data.access_token || data.jwt);
                if (t) {
                  setToken(t);
                  window.__apipAuthorizedOnce = false;
                  applyAuthorizeWhenReady(\"after-login\");
                }
              }
            } catch (e) {}

            return res;
          };
        }

        // Install ASAP
        wrapFetchOnce();

        // Hook factory ASAP (poll)
        (function pollHook() {
          if (hookSwaggerUiBundle()) return;
          setTimeout(pollHook, 50);
        })();

        // Try authorize on load if token already exists
        applyAuthorizeWhenReady(\"page-load\");

        // ✅ IMPORTANT : comme ton /api/login renvoie 204 + Set-Cookie,
        // ton token n'est PAS lisible en JS (httpOnly) => il faut soit:
        // - activer extractor cookie côté backend (déjà fait), et là ça marche sans \"Authorize\",
        // - OU changer login pour renvoyer JSON {token} si tu veux remplir Swagger Authorize.
      })();
    </script>

    <script src=\"";
            // line 325
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/init-swagger-ui.js", ($context["assetPackage"] ?? null)), "html", null, true);
            yield "\"></script>
";
        }
        // line 327
        yield "
<script src=\"";
        // line 328
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/init-common-ui.js", ($context["assetPackage"] ?? null)), "html", null, true);
        yield "\" defer></script>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@ApiPlatform/SwaggerUi/index.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  557 => 328,  554 => 327,  549 => 325,  324 => 102,  319 => 99,  314 => 98,  309 => 96,  304 => 95,  302 => 94,  295 => 93,  286 => 64,  282 => 62,  275 => 61,  267 => 24,  265 => 23,  258 => 22,  251 => 17,  247 => 16,  243 => 15,  238 => 14,  231 => 13,  220 => 10,  213 => 9,  207 => 6,  200 => 5,  192 => 330,  190 => 93,  184 => 89,  173 => 88,  166 => 87,  159 => 86,  157 => 85,  153 => 83,  142 => 81,  138 => 80,  129 => 73,  124 => 71,  119 => 70,  117 => 69,  114 => 68,  112 => 61,  75 => 26,  73 => 22,  70 => 21,  68 => 20,  65 => 19,  63 => 13,  60 => 12,  58 => 9,  55 => 8,  53 => 5,  48 => 2,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@ApiPlatform/SwaggerUi/index.html.twig", "C:\\Users\\totot\\PhpstormProjects\\DailyFinance-API\\vendor\\api-platform\\symfony\\Bundle\\Resources\\views\\SwaggerUi\\index.html.twig");
    }
}
