<?php

/**
 * File containing the Location Handler interface.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace eZ\Publish\SPI\Persistence\Content\Location;

use eZ\Publish\SPI\Persistence\Content\Location;

/**
 * The Location Handler interface defines operations on Location elements in the storage engine.
 */
interface Handler
{
    /**
     * Loads the data for the location identified by $locationId.
     *
     * @param int $locationId
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Location
     */
    public function load($locationId);

    /**
     * Loads the subtree ids of the location identified by $locationId.
     *
     * @param int $locationId
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return array Location ids are in the index, Content ids in the value.
     */
    public function loadSubtreeIds($locationId);

    /**
     * Loads the data for the location identified by $remoteId.
     *
     * @param string $remoteId
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Location
     */
    public function loadByRemoteId($remoteId);

    /**
     * Loads all locations for $contentId, optionally limited to a sub tree
     * identified by $rootLocationId.
     *
     * @param int $contentId
     * @param int $rootLocationId
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Location[]
     */
    public function loadLocationsByContent($contentId, $rootLocationId = null);

    /**
     * Loads all parent Locations for unpublished Content by given $contentId.
     *
     *
     * @param mixed $contentId
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Location[]
     */
    public function loadParentLocationsForDraftContent($contentId);

    /**
     * Copy location object identified by $sourceId, into destination identified by $destinationParentId.
     *
     * Performs a deep copy of the location identified by $sourceId and all of
     * its child locations, copying the most recent published content object
     * for each location to a new content object without any additional version
     * information. Relations are not copied. URLs are not touched at all.
     *
     * @param mixed $sourceId
     * @param mixed $destinationParentId
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If $sourceId or $destinationParentId are invalid
     *
     * @return Location the newly created Location.
     */
    public function copySubtree($sourceId, $destinationParentId);

    /**
     * Moves location identified by $sourceId into new parent identified by $destinationParentId.
     *
     * Performs a full move of the location identified by $sourceId to a new
     * destination, identified by $destinationParentId. Relations do not need
     * to be updated, since they refer to Content. URLs are not touched.
     *
     * @param mixed $sourceId
     * @param mixed $destinationParentId
     *
     * @return bool
     */
    public function move($sourceId, $destinationParentId);

    /**
     * Marks the given nodes and all ancestors as modified.
     *
     * Optionally a time stamp with the modification date may be specified,
     * otherwise the current time is used.
     *
     * @param int|string $locationId
     * @param int $timestamp
     */
    public function markSubtreeModified($locationId, $timestamp = null);

    /**
     * Sets a location to be hidden, and it self + all children to invisible.
     *
     * @param mixed $id Location ID
     */
    public function hide($id);

    /**
     * Sets a location to be unhidden, and self + children to visible unless a parent is hiding the tree.
     * If not make sure only children down to first hidden node is marked visible.
     *
     * @param mixed $id
     */
    public function unHide($id);

    /**
     * Swaps the content object being pointed to by a location object.
     *
     * Make the location identified by $locationId1 refer to the Content
     * referred to by $locationId2 and vice versa.
     *
     * @param mixed $locationId1
     * @param mixed $locationId2
     *
     * @return bool
     */
    public function swap($locationId1, $locationId2);

    /**
     * Updates an existing location.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Location\UpdateStruct $location
     * @param int $locationId
     */
    public function update(UpdateStruct $location, $locationId);

    /**
     * Creates a new location rooted at $location->parentId.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Location\CreateStruct $location
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Location
     */
    public function create(CreateStruct $location);

    /**
     * Removes all Locations under and including $locationId.
     *
     * Performs a recursive delete on the location identified by $locationId,
     * including all of its child locations. Content which is not referred to
     * by any other location is automatically removed. Content which looses its
     * main Location will get the first of its other Locations assigned as the
     * new main Location.
     *
     * @param mixed $locationId
     *
     * @return bool
     */
    public function removeSubtree($locationId);

    /**
     * Set section on all content objects in the subtree.
     * Only main locations will be updated.
     *
     * @todo This can be confusing (regarding permissions and main/multi location).
     * So method is for the time being not in PublicAPI so people can instead
     * write scripts using their own logic against the assignSectionToContent() api.
     *
     * @param mixed $locationId
     * @param mixed $sectionId
     */
    public function setSectionForSubtree($locationId, $sectionId);

    /**
     * Changes main location of content identified by given $contentId to location identified by given $locationId.
     *
     * @param mixed $contentId
     * @param mixed $locationId
     */
    public function changeMainLocation($contentId, $locationId);
}
