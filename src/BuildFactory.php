<?php

namespace PHPCensor;

use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Store\Factory;

/**
 * BuildFactory - Takes in a generic "Build" and returns a type-specific build model.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildFactory
{
    /**
     * @param ConfigurationInterface $configuration
     * @param int                    $buildId
     *
     * @return Build|null
     *
     * @throws Exception\HttpException
     */
    public static function getBuildById(ConfigurationInterface $configuration, int $buildId): ?Build
    {
        $build = Factory::getStore('Build')->getById($buildId);

        if (empty($build)) {
            return null;
        }

        return self::getBuild($configuration, $build);
    }

    /**
     * Takes a generic build and returns a type-specific build model.
     *
     * @param ConfigurationInterface $configuration
     * @param Build                  $build
     *
     * @return Build
     *
     * @throws Exception\HttpException
     */
    public static function getBuild(
        ConfigurationInterface $configuration,
        Build $build
    ): Build {
        $project = $build->getProject();

        if (!empty($project)) {
            switch ($project->getType()) {
                case Project::TYPE_LOCAL:
                    $type = 'LocalBuild';
                    break;
                case Project::TYPE_GIT:
                    $type = 'GitBuild';
                    break;
                case Project::TYPE_GITHUB:
                    $type = 'GithubBuild';
                    break;
                case Project::TYPE_BITBUCKET:
                    $type = 'BitbucketBuild';
                    break;
                case Project::TYPE_GITLAB:
                    $type = 'GitlabBuild';
                    break;
                case Project::TYPE_GOGS:
                    $type = 'GogsBuild';
                    break;
                case Project::TYPE_HG:
                    $type = 'HgBuild';
                    break;
                case Project::TYPE_BITBUCKET_HG:
                    $type = 'BitbucketHgBuild';
                    break;
                case Project::TYPE_BITBUCKET_SERVER:
                    $type = 'BitbucketServerBuild';
                    break;
                case Project::TYPE_SVN:
                    $type = 'SvnBuild';
                    break;
                default:
                    return $build;
            }

            $class = '\\PHPCensor\\Model\\Build\\' . $type;
            $build = new $class($configuration, $build->getDataArray());
        }

        return $build;
    }
}
