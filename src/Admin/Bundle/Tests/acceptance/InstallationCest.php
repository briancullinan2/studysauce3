<?php
namespace Admin\Bundle\Tests;

use Codeception\Module\Doctrine2;
use StudySauce\Bundle\Entity\User;
use WebDriver;
use WebDriverBy;
use WebDriverKeys;

/**
 * Class EmailsCest
 * @package StudySauce\Bundle\Tests
 * @backupGlobals false
 * @backupStaticAttributes false
 */
class InstallationCest
{
    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
    }

    // tests

    /**
     * @param AcceptanceTester $I
     */
    public function tryInstall(AcceptanceTester $I)
    {
        $I->wantTo('install StudySauce on a new instance');
        $I->amOnUrl('https://aws.amazon.com/');
        $I->wait(1);
        $I->moveMouseOver('div[data-dropdown="aws-nav-dropdown-account"]');
        $I->click('AWS Management Console');
        $I->fillField('input[name="email"]', 'admin@studysauce.com');
        $I->fillField('input[name="password"]', '2StudyBetter!');
        $I->click('#signInSubmit-input');
        $I->wait(1);
        $I->click('a[data-service-id="ec2"]');
        $I->wait(1);
        $I->click('Launch Instance');
        $I->wait(1);
        $I->click('//div[contains(.,"AMI") and contains(@gwtuirendered,"gwt-uid")]//button');
        $I->wait(1);
        $I->click('//td[contains(.,"t2.large")][following-sibling::*]/..//label');
        $I->wait(1);
        $I->click('Configure Instance Details');
        $I->wait(1);
        $I->click('//span[contains(.,"Advanced Details")]');
        $I->wait(1);
        $update = file_get_contents(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'update_test.sh');
        $cert = "-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDOzzVPVDZgtYNy
P3yZdcPUu5U+NjyaK+fMdna09eBD1f5M+O1PSHCytzt2jd1Pd4m1l8WPRjdzsGT0
IPz53dJRjEEA0FoMoH40HXfTR0zcmRf44eBEf0toSAmKkvRQ8+YVMJHm0nnWhdrv
VWqCJVLeFSBq6ft6PV00gLhOoBj3Gw+KvPOwIjwPuVBltXENygXHsoXQltSp3V2G
NcBPAlj/vPPz9AnP0QbyhEhcISRQX5JSsJoHVjnFWNJZ7kLcOppmZWnQSrqRXAEn
tWFMKtpNz0EAgmiLbPfqBFDeSkDe6nxdLLUS0sYsp2iqIawCUHHMqZTw3T2juyyR
2idavUT5AgMBAAECggEBAM6u5Q9QETxbi2+tpT/VIw7DHedb2vsVcAa2SfKWXDhP
cGPKz5hRxFfHqcTVCN23kMgMU2PZ/+c93dbh9RFesCfRrNE8aRJ/f0FkRfHAKz+4
PO3+B91M/rbMb8SvEz6oUkTREq+FoEBV7DUOv3AsDwJmSMyw5SQImdKz5f6mH+0r
sNYAIlgz6Re7Gc7Z/xc+LSKtdcVXIGtx9RUB5oDn67/0WRdxL8+WkrodhPSk4tjB
c6zbXbxeuD89Gc2J6L5VviPUCqz5biHXOvWwe61pjJnJiWGBva7of2GtXmJPFPNG
gL4xynz/qNbzXFWER6ifePFfLDMLI/WUZZdINTY4sAECgYEA8RAofaqIUPAhQCcx
MWUgnOCqabs4rtmEu5xyw62u9IvReQXY0r6/loinb/xecHwhx6Bj+n81VUJtMNoK
xPgnkpUANm8IvUir/00QGT0sPV8/BwUaOe00bzI6HSV0PZHmTKCspgBbQUMUgOWr
+IxCeERLuhPR6OqN0f40j1/pevkCgYEA25+0JYD/dkwnqJTFlauGlDfG0LAU0eH8
8WrordyE/Fovdj2aDCLAv85E5SeLS+XEKbx4IZEz0ccbq8bQlbp2NHOfu8sF/wmv
C+hB6Y2zsIE1bftJCq8Y7JzVWFu9XUleLW1ze3kxBVWHJrErJe2XDdmeHhURhqoQ
sgpK3qoqmgECgYBDJKsChZM+aAP66G+tQGubBoCwvnMFUJTF5MeadS/78U3BFb3U
xUh710g7yuFLF2gZQDVYukHSo5PiPXkub6gmDdZnUvnuLuWpUH+haAaAeZ0GiYdK
hyVJq4XARIRh+ddZlI9CFWtVfCej11TU/8wrz2oARDD9XQdvbAybuq0/kQKBgQDU
sZT4YTaGbXhW5kV4DpaWAnJz9qMjJDYf44aVoiPUdM7UNxJyQFHlL7E/MA3SIiHY
vaKl94Z02dwtfqzQ5LTHVVbTuuoCtXEmGfeDZW0pOejxq1NwmmSL+dMP8ECzEHO5
kO8vHA5ieRMbYKdF4xPQIPnlbkf738WtdxRNEgWSAQKBgCOlN/NatUlb/dC4lcDM
G1swHsf5LpoQWdyEtCMFxPSmAsykGR61Qp3qCPgbjcZCuxGgbBqW3pdpOg+uWexl
6IGJEBXHXPztN2l/apa1cb8NMf3PVtvv2/w4BkboYadDrX/3CFG3gY0njYIco7XG
Xj77Msrev6eKbm9npWxUwtV3
-----END PRIVATE KEY-----
-----BEGIN CERTIFICATE-----
MIIFNTCCBB2gAwIBAgIIJLgRzFPOLogwDQYJKoZIhvcNAQELBQAwgbQxCzAJBgNV
BAYTAlVTMRAwDgYDVQQIEwdBcml6b25hMRMwEQYDVQQHEwpTY290dHNkYWxlMRow
GAYDVQQKExFHb0RhZGR5LmNvbSwgSW5jLjEtMCsGA1UECxMkaHR0cDovL2NlcnRz
LmdvZGFkZHkuY29tL3JlcG9zaXRvcnkvMTMwMQYDVQQDEypHbyBEYWRkeSBTZWN1
cmUgQ2VydGlmaWNhdGUgQXV0aG9yaXR5IC0gRzIwHhcNMTYwNDE5MTY0NDM4WhcN
MTcwNDI0MjExNjM5WjA+MSEwHwYDVQQLExhEb21haW4gQ29udHJvbCBWYWxpZGF0
ZWQxGTAXBgNVBAMMECouc3R1ZHlzYXVjZS5jb20wggEiMA0GCSqGSIb3DQEBAQUA
A4IBDwAwggEKAoIBAQDOzzVPVDZgtYNyP3yZdcPUu5U+NjyaK+fMdna09eBD1f5M
+O1PSHCytzt2jd1Pd4m1l8WPRjdzsGT0IPz53dJRjEEA0FoMoH40HXfTR0zcmRf4
4eBEf0toSAmKkvRQ8+YVMJHm0nnWhdrvVWqCJVLeFSBq6ft6PV00gLhOoBj3Gw+K
vPOwIjwPuVBltXENygXHsoXQltSp3V2GNcBPAlj/vPPz9AnP0QbyhEhcISRQX5JS
sJoHVjnFWNJZ7kLcOppmZWnQSrqRXAEntWFMKtpNz0EAgmiLbPfqBFDeSkDe6nxd
LLUS0sYsp2iqIawCUHHMqZTw3T2juyyR2idavUT5AgMBAAGjggG+MIIBujAMBgNV
HRMBAf8EAjAAMB0GA1UdJQQWMBQGCCsGAQUFBwMBBggrBgEFBQcDAjAOBgNVHQ8B
Af8EBAMCBaAwNwYDVR0fBDAwLjAsoCqgKIYmaHR0cDovL2NybC5nb2RhZGR5LmNv
bS9nZGlnMnMxLTIyNi5jcmwwXQYDVR0gBFYwVDBIBgtghkgBhv1tAQcXATA5MDcG
CCsGAQUFBwIBFitodHRwOi8vY2VydGlmaWNhdGVzLmdvZGFkZHkuY29tL3JlcG9z
aXRvcnkvMAgGBmeBDAECATB2BggrBgEFBQcBAQRqMGgwJAYIKwYBBQUHMAGGGGh0
dHA6Ly9vY3NwLmdvZGFkZHkuY29tLzBABggrBgEFBQcwAoY0aHR0cDovL2NlcnRp
ZmljYXRlcy5nb2RhZGR5LmNvbS9yZXBvc2l0b3J5L2dkaWcyLmNydDAfBgNVHSME
GDAWgBRAwr0njsw0gzCiM9f7bLPwtCyAzjArBgNVHREEJDAighAqLnN0dWR5c2F1
Y2UuY29tgg5zdHVkeXNhdWNlLmNvbTAdBgNVHQ4EFgQUtngMwSgHBCiL2q6oOGQq
oMF+izEwDQYJKoZIhvcNAQELBQADggEBAC1WRCMhr8qDQm3TPEUa7YCUo45S6btP
TJAujCby9XXe3bOpN7sOO1aSJyrZfF7TxXrRsKDmCoXQSAW6raoYu24Nwv47R4i8
esneNWioJ1CHE8Uaukp54lb7ZG8V4EjspD6vzIhPHZzDTofoA0em5ZLTGYfHAcdY
o46X1F7iiuXcdTaLBuY3y3PRMQV20OjbAt7PBlEg49Dba6jObaIZxYVWy806RXTv
j/vGPk8Mz90xUzicWQHDBemeyO4SOQN6mzHpuW+c6rJhWgWslRDNYi6xSqIeRSCL
1GBb+Ldm2509OZT1JRpkiOaM0Y1JiS4Bw05wXHcqYg40gbBb5YeDv0A=
-----END CERTIFICATE-----
";
        $bundle = "-----BEGIN CERTIFICATE-----
MIIE0DCCA7igAwIBAgIBBzANBgkqhkiG9w0BAQsFADCBgzELMAkGA1UEBhMCVVMx
EDAOBgNVBAgTB0FyaXpvbmExEzARBgNVBAcTClNjb3R0c2RhbGUxGjAYBgNVBAoT
EUdvRGFkZHkuY29tLCBJbmMuMTEwLwYDVQQDEyhHbyBEYWRkeSBSb290IENlcnRp
ZmljYXRlIEF1dGhvcml0eSAtIEcyMB4XDTExMDUwMzA3MDAwMFoXDTMxMDUwMzA3
MDAwMFowgbQxCzAJBgNVBAYTAlVTMRAwDgYDVQQIEwdBcml6b25hMRMwEQYDVQQH
EwpTY290dHNkYWxlMRowGAYDVQQKExFHb0RhZGR5LmNvbSwgSW5jLjEtMCsGA1UE
CxMkaHR0cDovL2NlcnRzLmdvZGFkZHkuY29tL3JlcG9zaXRvcnkvMTMwMQYDVQQD
EypHbyBEYWRkeSBTZWN1cmUgQ2VydGlmaWNhdGUgQXV0aG9yaXR5IC0gRzIwggEi
MA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC54MsQ1K92vdSTYuswZLiBCGzD
BNliF44v/z5lz4/OYuY8UhzaFkVLVat4a2ODYpDOD2lsmcgaFItMzEUz6ojcnqOv
K/6AYZ15V8TPLvQ/MDxdR/yaFrzDN5ZBUY4RS1T4KL7QjL7wMDge87Am+GZHY23e
cSZHjzhHU9FGHbTj3ADqRay9vHHZqm8A29vNMDp5T19MR/gd71vCxJ1gO7GyQ5HY
pDNO6rPWJ0+tJYqlxvTV0KaudAVkV4i1RFXULSo6Pvi4vekyCgKUZMQWOlDxSq7n
eTOvDCAHf+jfBDnCaQJsY1L6d8EbyHSHyLmTGFBUNUtpTrw700kuH9zB0lL7AgMB
AAGjggEaMIIBFjAPBgNVHRMBAf8EBTADAQH/MA4GA1UdDwEB/wQEAwIBBjAdBgNV
HQ4EFgQUQMK9J47MNIMwojPX+2yz8LQsgM4wHwYDVR0jBBgwFoAUOpqFBxBnKLbv
9r0FQW4gwZTaD94wNAYIKwYBBQUHAQEEKDAmMCQGCCsGAQUFBzABhhhodHRwOi8v
b2NzcC5nb2RhZGR5LmNvbS8wNQYDVR0fBC4wLDAqoCigJoYkaHR0cDovL2NybC5n
b2RhZGR5LmNvbS9nZHJvb3QtZzIuY3JsMEYGA1UdIAQ/MD0wOwYEVR0gADAzMDEG
CCsGAQUFBwIBFiVodHRwczovL2NlcnRzLmdvZGFkZHkuY29tL3JlcG9zaXRvcnkv
MA0GCSqGSIb3DQEBCwUAA4IBAQAIfmyTEMg4uJapkEv/oV9PBO9sPpyIBslQj6Zz
91cxG7685C/b+LrTW+C05+Z5Yg4MotdqY3MxtfWoSKQ7CC2iXZDXtHwlTxFWMMS2
RJ17LJ3lXubvDGGqv+QqG+6EnriDfcFDzkSnE3ANkR/0yBOtg2DZ2HKocyQetawi
DsoXiWJYRBuriSUBAA/NxBti21G00w9RKpv0vHP8ds42pM3Z2Czqrpv1KrKQ0U11
GIo/ikGQI31bS/6kA1ibRrLDYGCD+H1QQc7CoZDDu+8CL9IVVO5EFdkKrqeKM+2x
LXY2JtwE65/3YR8V3Idv7kaWKK2hJn0KCacuBKONvPi8BDAB
-----END CERTIFICATE-----
-----BEGIN CERTIFICATE-----
MIIEfTCCA2WgAwIBAgIDG+cVMA0GCSqGSIb3DQEBCwUAMGMxCzAJBgNVBAYTAlVT
MSEwHwYDVQQKExhUaGUgR28gRGFkZHkgR3JvdXAsIEluYy4xMTAvBgNVBAsTKEdv
IERhZGR5IENsYXNzIDIgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkwHhcNMTQwMTAx
MDcwMDAwWhcNMzEwNTMwMDcwMDAwWjCBgzELMAkGA1UEBhMCVVMxEDAOBgNVBAgT
B0FyaXpvbmExEzARBgNVBAcTClNjb3R0c2RhbGUxGjAYBgNVBAoTEUdvRGFkZHku
Y29tLCBJbmMuMTEwLwYDVQQDEyhHbyBEYWRkeSBSb290IENlcnRpZmljYXRlIEF1
dGhvcml0eSAtIEcyMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAv3Fi
CPH6WTT3G8kYo/eASVjpIoMTpsUgQwE7hPHmhUmfJ+r2hBtOoLTbcJjHMgGxBT4H
Tu70+k8vWTAi56sZVmvigAf88xZ1gDlRe+X5NbZ0TqmNghPktj+pA4P6or6KFWp/
3gvDthkUBcrqw6gElDtGfDIN8wBmIsiNaW02jBEYt9OyHGC0OPoCjM7T3UYH3go+
6118yHz7sCtTpJJiaVElBWEaRIGMLKlDliPfrDqBmg4pxRyp6V0etp6eMAo5zvGI
gPtLXcwy7IViQyU0AlYnAZG0O3AqP26x6JyIAX2f1PnbU21gnb8s51iruF9G/M7E
GwM8CetJMVxpRrPgRwIDAQABo4IBFzCCARMwDwYDVR0TAQH/BAUwAwEB/zAOBgNV
HQ8BAf8EBAMCAQYwHQYDVR0OBBYEFDqahQcQZyi27/a9BUFuIMGU2g/eMB8GA1Ud
IwQYMBaAFNLEsNKR1EwRcbNhyz2h/t2oatTjMDQGCCsGAQUFBwEBBCgwJjAkBggr
BgEFBQcwAYYYaHR0cDovL29jc3AuZ29kYWRkeS5jb20vMDIGA1UdHwQrMCkwJ6Al
oCOGIWh0dHA6Ly9jcmwuZ29kYWRkeS5jb20vZ2Ryb290LmNybDBGBgNVHSAEPzA9
MDsGBFUdIAAwMzAxBggrBgEFBQcCARYlaHR0cHM6Ly9jZXJ0cy5nb2RhZGR5LmNv
bS9yZXBvc2l0b3J5LzANBgkqhkiG9w0BAQsFAAOCAQEAWQtTvZKGEacke+1bMc8d
H2xwxbhuvk679r6XUOEwf7ooXGKUwuN+M/f7QnaF25UcjCJYdQkMiGVnOQoWCcWg
OJekxSOTP7QYpgEGRJHjp2kntFolfzq3Ms3dhP8qOCkzpN1nsoX+oYggHFCJyNwq
9kIDN0zmiN/VryTyscPfzLXs4Jlet0lUIDyUGAzHHFIYSaRt4bNYC8nY7NmuHDKO
KHAN4v6mF56ED71XcLNa6R+ghlO773z/aQvgSMO3kwvIClTErF0UZzdsyqUvMQg3
qm5vjLyb4lddJIGvl5echK1srDdMZvNhkREg5L4wn3qkKQmw4TRfZHcYQFHfjDCm
rw==
-----END CERTIFICATE-----
-----BEGIN CERTIFICATE-----
MIIEADCCAuigAwIBAgIBADANBgkqhkiG9w0BAQUFADBjMQswCQYDVQQGEwJVUzEh
MB8GA1UEChMYVGhlIEdvIERhZGR5IEdyb3VwLCBJbmMuMTEwLwYDVQQLEyhHbyBE
YWRkeSBDbGFzcyAyIENlcnRpZmljYXRpb24gQXV0aG9yaXR5MB4XDTA0MDYyOTE3
MDYyMFoXDTM0MDYyOTE3MDYyMFowYzELMAkGA1UEBhMCVVMxITAfBgNVBAoTGFRo
ZSBHbyBEYWRkeSBHcm91cCwgSW5jLjExMC8GA1UECxMoR28gRGFkZHkgQ2xhc3Mg
MiBDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTCCASAwDQYJKoZIhvcNAQEBBQADggEN
ADCCAQgCggEBAN6d1+pXGEmhW+vXX0iG6r7d/+TvZxz0ZWizV3GgXne77ZtJ6XCA
PVYYYwhv2vLM0D9/AlQiVBDYsoHUwHU9S3/Hd8M+eKsaA7Ugay9qK7HFiH7Eux6w
wdhFJ2+qN1j3hybX2C32qRe3H3I2TqYXP2WYktsqbl2i/ojgC95/5Y0V4evLOtXi
EqITLdiOr18SPaAIBQi2XKVlOARFmR6jYGB0xUGlcmIbYsUfb18aQr4CUWWoriMY
avx4A6lNf4DD+qta/KFApMoZFv6yyO9ecw3ud72a9nmYvLEHZ6IVDd2gWMZEewo+
YihfukEHU1jPEX44dMX4/7VpkI+EdOqXG68CAQOjgcAwgb0wHQYDVR0OBBYEFNLE
sNKR1EwRcbNhyz2h/t2oatTjMIGNBgNVHSMEgYUwgYKAFNLEsNKR1EwRcbNhyz2h
/t2oatTjoWekZTBjMQswCQYDVQQGEwJVUzEhMB8GA1UEChMYVGhlIEdvIERhZGR5
IEdyb3VwLCBJbmMuMTEwLwYDVQQLEyhHbyBEYWRkeSBDbGFzcyAyIENlcnRpZmlj
YXRpb24gQXV0aG9yaXR5ggEAMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQAD
ggEBADJL87LKPpH8EsahB4yOd6AzBhRckB4Y9wimPQoZ+YeAEW5p5JYXMP80kWNy
OO7MHAGjHZQopDH2esRU1/blMVgDoszOYtuURXO1v0XJJLXVggKtI3lpjbi2Tc7P
TMozI+gciKqdi0FuFskg5YmezTvacPd+mSYgFFQlq25zheabIZ0KbIIOqPjCDPoQ
HmyW74cNxA9hi63ugyuV+I6ShHI56yDqg+2DzZduCLzrTia2cyvk0/ZM/iZx4mER
dEr/VxqHD3VILs9RaRegAhJhldXRQLIQTO7ErBBDpqWeCtWVYpoNz4iCxTIM5Cuf
ReYNnyicsbkqWletNw+vHX/bvZ8=
-----END CERTIFICATE-----
";
        $bash = <<<EOSH
#!/bin/bash

mkdir /var/www
cd /var/www
yum update -y
yum install -y mysql-server httpd24 php55 php55-mysqlnd php55-pdo mod24_ssl openssl php55-mbstring php55-mcrypt php55-common php-apc php55-gd php55-xml libjpeg libpng git fontconfig libXrender libXext icu xorg-x11-fonts-Type1 xorg-x11-fonts-75dpi freetype libpng zlib libjpeg-turbo openssl
cd /tmp/
wget --no-check-certificate http://download.gna.org/wkhtmltopdf/0.12/0.12.2.1/wkhtmltox-0.12.2.1_linux-centos6-amd64.rpm
rpm -ivh /tmp/wkhtmltox-0.12.2.1_linux-centos6-amd64.rpm
ln -s /usr/local/bin/wkhtmltopdf /bin/wkhtmltopdf

wget --no-check-certificate -O - https://curl.haxx.se/ca/cacert.pem > /etc/pki/tls/certs/ca-bundle.crt

export GIT_SSL_NO_VERIFY=true
cd /var/www/
git clone https://bjcullinan:Da1ddy23@bitbucket.org/StudySauce/studysauce3.git

chown -R mysql:mysql /var/lib/mysql
service mysqld start
/usr/bin/mysqladmin -u root password '9MiIsEf42mnEXx0n'
echo "CREATE DATABASE studysauce3; GRANT ALL ON studysauce3.* TO 'study2'@'localhost' IDENTIFIED BY 'itekIO^#(1234';" | mysql -u root --password=9MiIsEf42mnEXx0n -h localhost

echo "* * * * * apache /var/www/studysauce3/cron.sh" >> /etc/crontab
chmod a+x /var/www/studysauce3/cron.sh
echo "
127.0.0.1  studysauce.com
127.0.0.1  test.studysauce.com
" >> /etc/hosts
echo "
<Directory \"/var/www/html\">
    AllowOverride All
</Directory>
" >> /etc/httpd/conf/httpd.conf
sed -i "s/^;date.timezone =$/date.timezone = \"US\/Arizona\"/" /etc/php.ini |grep "^timezone" /etc/php.ini
sed -i "s/^memory_limit = 128M$/memory_limit = 256M/" /etc/php.ini |grep "^memory_limit" /etc/php.ini
sed -i "s/^#SSLCACertificateFile/SSLCACertificateFile/" /etc/httpd/conf.d/ssl.conf |grep "SSLCACertificateFile" /etc/httpd/conf.d/ssl.conf
sed -i "s/^SSLCertificateKeyFile/#SSLCertificateKeyFile/" /etc/httpd/conf.d/ssl.conf |grep "SSLCertificateKeyFile" /etc/httpd/conf.d/ssl.conf
echo "$cert" > /etc/pki/tls/certs/localhost.crt
echo "$bundle" >> /etc/pki/tls/certs/ca-bundle.crt
rm -R /var/www/html
ln -s /var/www/studysauce3/web /var/www/html

service httpd restart
chkconfig httpd on

$update
EOSH;

        $I->fillField('textarea', $bash);
        $I->click('Add Storage');
        $I->wait(1);
        $I->click('Tag Instance');
        $I->wait(1);
        $I->click('Configure Security Group');
        $I->wait(1);
        $I->click('//label[contains(.,"existing")]');
        $I->wait(2);
        $I->click('//tr[contains(.,"sg-a416bfc1")]//label');
        $I->wait(1);
        // add security group
        // $I->click('//tr[contains(.,"sg-a416bfc1")]//a[contains(.,"Copy to new")]');
        // $I->wait(1);
        $I->click('Review and Launch');
        $I->wait(1);
        $I->click('Launch');
        $I->wait(1);
        $I->click('.dialogMiddle input[type="checkbox"]');
        $I->click('Launch Instances');
        $I->wait(3);
        $instanceId = $I->grabTextFrom('a[href*="Instances:search"]');
        $I->click('View Instances');
        $I->wait(30);
        // change public IP
        $I->click('Elastic IPs');
        $I->wait(1);
        $I->click('//tr[contains(.,"54.201.44.140")]//label');
        $I->wait(1);
        $I->click('//button[contains(.,"Actions")]');
        $I->wait(1);
        $I->click('//div[contains(.,"Associate Address") and contains(@role,"menuitem")]');
        $I->wait(1);
        $I->fillField('.dialogMiddle input[placeholder*="Search"]', $instanceId);
        $I->wait(1);
        $I->click('.dialogMiddle input[placeholder*="Search"]');
        $I->wait(1);
        $I->click('//div[contains(.,"' . $instanceId . '")]');
        $I->checkOption('.dialogMiddle input[type="checkbox"]'); // reassociate IP even if its already assigned
        $I->wait(1);
        $I->click('Associate');
        $I->wait(1);
        $I->click('Instances');
        $I->wait(900);
        $I->amOnUrl('https://test.studysauce.com');
        $I->click('Sign in');
        $I->fillField('.email input', 'admin@studysauce.com');
        $I->fillField('.password input', 'this computer is 1337');
        $I->click('.highlighted-link button');
        $I->wait(5);
        $I->amOnPage('/validation');
        $I->click('//tr[contains(.,"Deploy")]//a[contains(@href,"run-test")]');
        $I->wait(900);
    }

    public function tryDeploy(AcceptanceTester $I) {
        $I->wantTo('test everything before deployment');
        $I->test('tryCheckSettings');
        $I->test('tryLandingPages');
        $I->test('tryBillMyParents');
        $I->test('tryDetailedNotes');
        $I->click('a[href*="/logout"]');
        $I->test('tryAdviserLogin');
        $I->test('tryGroupInvite');
        $I->test('tryGroupDeadlines');
        $I->click('a[href*="/logout"]');
        $I->test('tryGuestCheckout');
        $I->test('tryDetailedPlan');
        $I->click('a[href*="/logout"]');
        $I->test('tryGuestCheckout');
        $I->test('tryFreeCourse');
        $I->test('tryAllCourse2');
        $I->test('tryAllCourse3');
        $I->click('a[href*="/logout"]');
        $I->test('tryAllEmails');
        $I->test('tryPushToProduction');
    }

    /**
     * @depends tryLandingPages
     * @depends tryBillMyParents
     * @depends tryDetailedNotes
     * @depends tryGuestCheckout
     * @depends tryDetailedPlan
     * @depends tryGuestCheckout
     * @depends tryFreeCourse
     * @depends tryAllCourse2
     * @depends tryAllCourse3
     * @depends tryAllEmails
     * @param AcceptanceTester $I
     */
    public function tryPushToProduction(AcceptanceTester $I)
    {
        $I->wantTo('log in to AWS to re-associate IP address to new version and copy database to production');

    }
}