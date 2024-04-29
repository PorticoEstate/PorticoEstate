interface SearchDataAllApiResponse {
    activities: Activity[];
    buildings: Building[];
    building_resources: BuildingResource[];
    facilities: Facility[];
    resources: Resource[];
    resource_activities: ResourceActivity[];
    resource_facilities: ResourceFacility[];
    resource_categories: ResourceCategory[];
    resource_category_activity: ResourceCategoryActivity[];
    towns: Town[];
    organizations: Organization[];
}

interface Activity {
    id: number;
    parent_id: number | null;
    name: string;
    description: string;
    active: number;
}

interface Building {
    id: number;
    activity_id: number;
    deactivate_calendar: number;
    deactivate_application: number;
    deactivate_sendmessage: number;
    extra_kalendar: number;
    name: string;
    homepage: string;
    location_code: string;
    phone: string;
    email: string;
    tilsyn_name: string;
    tilsyn_phone: string;
    tilsyn_email: string;
    tilsyn_name2: string;
    tilsyn_phone2: string;
    tilsyn_email2: string;
    street: string;
    zip_code: string;
    district: string;
    city: string;
    calendar_text: string;
    opening_hours: string;
}

interface BuildingResource {
    building_id: number;
    resource_id: number;
}

interface Facility {
    id: number;
    name: string;
    active: number;
}

interface Resource {
    id: number;
    active: number;
    name: string;
    activity_id: number;
    sort: number;
    organizations_ids: string;
    json_representation: string;
    rescategory_id: number;
    opening_hours: string;
    contact_info: string;
    direct_booking: number | null;
    direct_booking_season_id: number | null;
    simple_booking: number | null;
    booking_day_default_lenght: number;
    booking_dow_default_start: number;
    booking_time_default_start: number;
    booking_time_default_end: number;
    booking_time_minutes: number;
    booking_limit_number: number;
    booking_limit_number_horizont: number;
    simple_booking_start_date: string | null;
    simple_booking_end_date: string | null;
    booking_month_horizon: number;
    booking_day_horizon: number;
    capacity: number;
    deactivate_application: number;
    hidden_in_frontend: number;
    activate_prepayment: number;
    deactivate_calendar: number;
    booking_buffer_deadline: number;
    description_json: string;
}

interface ResourceActivity {
    resource_id: number;
    activity_id: number;
}

interface ResourceFacility {
    resource_id: number;
    facility_id: number;
}

interface ResourceCategory {
    id: number;
    parent_id: number | null;
    name: string;
    capacity: number | null;
    e_lock: number;
    active: number;
}

interface ResourceCategoryActivity {
    rescategory_id: number;
    activity_id: number;
}

interface Town {
    b_id: number;
    b_name: string;
    id: number;
    name: string;
}

interface Organization {
    id: number;
    organization_number: string;
    name: string;
    homepage: string;
    phone: string;
    email: string;
    co_address: string | null;
    street: string;
    zip_code: string;
    district: string;
    city: string;
    activity_id: number;
    show_in_portal: number;
}

interface RemoteInstance {
    id: number,
    location_id: number,
    name: string,
    webservicehost: string;
}