namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * Set to * to trust all proxies (Docker/Nginx setup).
     */
    protected $proxies = '*';

    /**
     * Trust all X-Forwarded headers including X-Forwarded-Proto.
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}