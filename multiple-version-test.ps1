$ver = "7.2","7.3","7.4","8.0"

$pwd = Get-Location

Foreach($v in $ver)
{
    $command = "docker run --rm --volume=$pwd/:/home/app --workdir=/home/app php:$v php vendor/bin/phpunit"
    Invoke-Expression -Command $command -OutVariable out
}