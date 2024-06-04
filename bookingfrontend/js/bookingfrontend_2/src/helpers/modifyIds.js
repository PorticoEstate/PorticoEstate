
/**
 * Modifies the IDs in the SearchDataAllApiResponse object to prevent collisions
 * between different instances of the application.
 *
 * @param {SearchDataAllApiResponse} response - The response object from the API.
 * @param {RemoteInstance} remoteInstance - the remote instance.
 * @returns {SearchDataAllApiResponse} The modified response object.
 */
export function ModifyIds(response, remoteInstance) {
    // Helper function to modify an ID.
    const prefix = remoteInstance.name;
    const modifyId = (id) => `${prefix}-${id}`;

    // Modify IDs in each array of objects.
    const modifiedResponse = {
        ...response,
        activities: response.activities.map((activity) => ({
            ...activity,
            original_id: activity.id,
            id: modifyId(activity.id),
            original_parent_id: activity.parent_id,
            parent_id: activity.parent_id ? modifyId(activity.parent_id) : null,
        })),
        buildings: response.buildings.map((building) => ({
            ...building,
            original_id: building.id,
            id: modifyId(building.id),
            original_activity_id: building.activity_id,
            activity_id: modifyId(building.activity_id),
            remoteInstance: remoteInstance
        })),
        building_resources: response.building_resources.map((buildingResource) => ({
            ...buildingResource,
            original_building_id: buildingResource.building_id,
            building_id: modifyId(buildingResource.building_id),
            original_resource_id: buildingResource.resource_id,
            resource_id: modifyId(buildingResource.resource_id),
        })),
        facilities: response.facilities.map((facility) => ({
            ...facility,
            original_id: facility.id,
            id: modifyId(facility.id),
        })),
        resources: response.resources.map((resource) => ({
            ...resource,
            original_id: resource.id,
            id: modifyId(resource.id),
            original_rescategory_id: resource.rescategory_id,
            rescategory_id: modifyId(resource.rescategory_id),
            remoteInstance: remoteInstance
        })),
        resource_activities: response.resource_activities.map((resourceActivity) => ({
            ...resourceActivity,
            original_resource_id: resourceActivity.resource_id,
            resource_id: modifyId(resourceActivity.resource_id),
            original_activity_id: resourceActivity.activity_id,
            activity_id: modifyId(resourceActivity.activity_id),
        })),
        resource_facilities: response.resource_facilities.map((resourceFacility) => ({
            ...resourceFacility,
            original_resource_id: resourceFacility.resource_id,
            resource_id: modifyId(resourceFacility.resource_id),
            original_facility_id: resourceFacility.facility_id,
            facility_id: modifyId(resourceFacility.facility_id),
        })),
        resource_categories: response.resource_categories.map((resourceCategory) => ({
            ...resourceCategory,
            original_id: resourceCategory.id,
            id: modifyId(resourceCategory.id),
            original_parent_id: resourceCategory.parent_id,
            parent_id: resourceCategory.parent_id ? modifyId(resourceCategory.parent_id) : null,
        })),
        resource_category_activity: response.resource_category_activity.map((resourceCategoryActivity) => ({
            ...resourceCategoryActivity,
            original_rescategory_id: resourceCategoryActivity.rescategory_id,
            rescategory_id: modifyId(resourceCategoryActivity.rescategory_id),
            original_activity_id: resourceCategoryActivity.activity_id,
            activity_id: modifyId(resourceCategoryActivity.activity_id),
        })),
        towns: response.towns.map((town) => ({
            ...town,
            original_b_id: town.b_id,
            b_id: modifyId(town.b_id),
            original_id: town.id,
            id: modifyId(town.id),
            remoteInstance: remoteInstance

        })),
        organizations: response.organizations.map((organization) => ({
            ...organization,
            original_id: organization.id,
            id: modifyId(organization.id),
            original_activity_id: organization.activity_id,
            activity_id: modifyId(organization.activity_id),
            remoteInstance: remoteInstance
        })),
    };


    return modifiedResponse;
}