param(
    [string] $OutputDir = "deploy"
)

$ErrorActionPreference = "Stop"
Set-StrictMode -Version Latest

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ThemeRoot = Resolve-Path (Join-Path $ScriptDir "..")
$ThemeName = "hands-and-vision"
$ResolvedOutputDir = Join-Path $ThemeRoot $OutputDir
$StagingRoot = Join-Path $ResolvedOutputDir ".theme-package"
$PackageRoot = Join-Path $StagingRoot $ThemeName
$ZipPath = Join-Path $ResolvedOutputDir "$ThemeName-theme-folder.zip"

$RequiredFiles = @(
    "style.css",
    "functions.php",
    "index.php",
    "single-service.php",
    "theme.json"
)

$RequiredDirectories = @(
    "assets",
    "inc",
    "template-parts",
    "languages",
    "woocommerce"
)

$ExcludedNames = @(
    ".git",
    ".github",
    ".agent",
    ".vscode",
    "node_modules",
    "vendor",
    "logs",
    "deploy",
    "backups",
    "docker",
    "docs"
)

function Assert-InsideThemeRoot {
    param([string] $Path)

    $ResolvedPath = [System.IO.Path]::GetFullPath($Path)
    $ResolvedRoot = [System.IO.Path]::GetFullPath($ThemeRoot)

    if (-not $ResolvedPath.StartsWith($ResolvedRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw "Refusing to remove a path outside the theme root: $ResolvedPath"
    }
}

foreach ($File in $RequiredFiles) {
    $Path = Join-Path $ThemeRoot $File
    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) {
        throw "Missing required theme file: $File"
    }
}

foreach ($Directory in $RequiredDirectories) {
    $Path = Join-Path $ThemeRoot $Directory
    if (-not (Test-Path -LiteralPath $Path -PathType Container)) {
        throw "Missing required theme directory: $Directory"
    }
}

$StyleHeader = Get-Content -LiteralPath (Join-Path $ThemeRoot "style.css") -Raw
if ($StyleHeader -notmatch "Theme Name:\s*Hand and Vision") {
    throw "style.css exists, but its WordPress theme header is missing or invalid."
}

New-Item -ItemType Directory -Force -Path $ResolvedOutputDir | Out-Null

if (Test-Path -LiteralPath $StagingRoot) {
    Assert-InsideThemeRoot -Path $StagingRoot
    Remove-Item -LiteralPath $StagingRoot -Recurse -Force
}

New-Item -ItemType Directory -Force -Path $PackageRoot | Out-Null

Get-ChildItem -LiteralPath $ThemeRoot -Force | ForEach-Object {
    if ($ExcludedNames -contains $_.Name) {
        return
    }

    Copy-Item -LiteralPath $_.FullName -Destination $PackageRoot -Recurse -Force
}

if (Test-Path -LiteralPath $ZipPath) {
    Assert-InsideThemeRoot -Path $ZipPath
    Remove-Item -LiteralPath $ZipPath -Force
}

Compress-Archive -LiteralPath $PackageRoot -DestinationPath $ZipPath -Force

Add-Type -AssemblyName System.IO.Compression.FileSystem
$Zip = [System.IO.Compression.ZipFile]::OpenRead($ZipPath)
try {
    $ZipEntries = $Zip.Entries | ForEach-Object { $_.FullName }
} finally {
    $Zip.Dispose()
}

$RequiredZipFiles = @(
    "$ThemeName/style.css",
    "$ThemeName/functions.php",
    "$ThemeName/index.php",
    "$ThemeName/single-service.php"
)

foreach ($Entry in $RequiredZipFiles) {
    if ($ZipEntries -notcontains $Entry) {
        throw "Package verification failed. Missing in zip: $Entry"
    }
}

$RequiredZipDirectories = @(
    "$ThemeName/assets/",
    "$ThemeName/inc/",
    "$ThemeName/template-parts/"
)

foreach ($Directory in $RequiredZipDirectories) {
    $HasDirectoryContent = $false
    foreach ($Entry in $ZipEntries) {
        if ($Entry.StartsWith($Directory, [System.StringComparison]::OrdinalIgnoreCase)) {
            $HasDirectoryContent = $true
            break
        }
    }

    if (-not $HasDirectoryContent) {
        throw "Package verification failed. Missing directory content in zip: $Directory"
    }
}

Write-Host "Theme package created successfully:"
Write-Host $ZipPath
