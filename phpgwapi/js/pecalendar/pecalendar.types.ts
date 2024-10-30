interface IEvent {
    type: 'booking' | 'allocation' | 'event' | 'temporary'
    // allocation -> booking -> event | temporary
    id: number
    id_string?: string
    active: number
    building_id: any
    application_id?: number
    completed: number
    name: string
    shortname?: string
    organization_id?: number
    resources: IBuildingResource[]
    season_id?: number
    season_name?: string
    // from: string
    // to: string
    _from: string
    _to: string
    // date: string
    building_name: string
    allocation_id?: number
    group_id?: number
    activity_id?: number
    activity_name?: string
    group_name?: string
    group_shortname?: string
    reminder?: number
    dates?: IEventDate[]
    homepage?: string
    description?: string
    equipment?: string
    access_requested?: number
    is_public?: number
}

export interface IBuildingResource {
    active: number
    id: number
    activity_id: number
    activity_name: string
    name: string
    simple_booking: 0 | 1
}

export interface IEventDate {
    from_: string
    to_: string
    id: number
}


export interface IBuilding {
    name: string
    type: 'lokale' | 'anlegg' | string;
    id: number
    menuaction: 'bookingfrontend.uiresource.show'
}




export interface ResultSet {
    totalResultsAvailable: number
    Result: Result
}

export interface Result {
    total_records: number
    results: SchedulingResults
}

export interface SchedulingResults {
    schedule: IEvent[]
    resources: Record<string, IBuildingResource>
    seasons: Season[]
}


export interface Season {
    id: number
    building_id: number
    name: string
    sfrom: string
    sto: string
    wday: number
    from_: string
    to_: string
}


export interface IFreeTimeSlot {
    when: string
    start: string
    end: string
    overlap: false | 1 | 2 | 3 // false = ledig | 1 = bestilt av ein anna | 2 = påbegynt/reservert | 3 = fortid
    applicationLink: ApplicationLink
}

export interface ApplicationLink {
    menuaction: string
    resource_id: number
    building_id: number
    "from_[]": string
    "to_[]": string
    simple: boolean
}
