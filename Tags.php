<?php
/*************************************************************************************/
/*                                                                                   */
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*                                                                                   */
/*************************************************************************************/

namespace Tags;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Tags\Model\TagsQuery;
use Thelia\Core\Install\Database;
use Thelia\Module\BaseModule;
use TheliaSmarty\Template\AbstractSmartyPlugin;

class Tags extends BaseModule
{
    /** @var string */
    public const DOMAIN_NAME = 'tags';

    public const DELETE_ORPHAN_EVENT = 'tags.delete_orphan_event';

    public function postActivation(?ConnectionInterface $con = null): void
    {
        if (! self::getConfigValue('is_initialised')) {
            $database = new Database($con->getWrappedConnection());
            $database->insertSql(null, array(__DIR__ . '/Config/thelia.sql'));

            self::setConfigValue('is_initialised', true);
        }
    }

    public function update($currentVersion, $newVersion, ?ConnectionInterface $con = null): void
    {
        $database = new Database($con->getWrappedConnection());

        if (version_compare($currentVersion, '1.2.0') === -1) {
            $database->insertSql(null, array(__DIR__ . '/Config/update1.1.sql'));
        }

        // 1.2.1 database update
        if (version_compare($newVersion, '1.2.1') === 0) {
            $database->insertSql(null, array(__DIR__ . '/Config/update1.2.1.sql'));
        }
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([
                __DIR__.'/I18n',
                __DIR__.'/I18n/*',
                __DIR__.'/I18n/**/*',
                __DIR__.'/Smarty',
            ])
            ->autowire(true)
            ->autoconfigure(true);

        // The Smarty plugin is an optional integration: it needs TheliaSmarty, which Thelia 3 no longer ships by default.
        if (class_exists(AbstractSmartyPlugin::class)) {
            $servicesConfigurator->load(self::getModuleCode().'\\Smarty\\', __DIR__.'/Smarty')
                ->autowire(true)
                ->autoconfigure(true);
        }
    }
}
