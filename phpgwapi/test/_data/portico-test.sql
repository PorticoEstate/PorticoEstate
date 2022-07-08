--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.8
-- Dumped by pg_dump version 9.6.8

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: seq_controller_check_item; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_check_item
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_check_item OWNER TO portico;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: controller_check_item; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_check_item (
    id integer DEFAULT nextval('public.seq_controller_check_item'::regclass) NOT NULL,
    control_item_id integer,
    check_list_id integer
);


ALTER TABLE public.controller_check_item OWNER TO portico;

--
-- Name: seq_controller_check_item_case; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_check_item_case
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_check_item_case OWNER TO portico;

--
-- Name: controller_check_item_case; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_check_item_case (
    id integer DEFAULT nextval('public.seq_controller_check_item_case'::regclass) NOT NULL,
    check_item_id integer NOT NULL,
    status integer NOT NULL,
    measurement character varying(50),
    location_id integer,
    location_item_id bigint,
    descr text,
    user_id integer,
    entry_date bigint NOT NULL,
    modified_date bigint,
    modified_by integer,
    location_code character varying(30),
    component_location_id integer,
    component_id integer
);


ALTER TABLE public.controller_check_item_case OWNER TO portico;

--
-- Name: seq_controller_check_item_status; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_check_item_status
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_check_item_status OWNER TO portico;

--
-- Name: controller_check_item_status; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_check_item_status (
    id integer DEFAULT nextval('public.seq_controller_check_item_status'::regclass) NOT NULL,
    name character varying(50) NOT NULL,
    open smallint,
    closed smallint,
    pending smallint,
    sorting integer
);


ALTER TABLE public.controller_check_item_status OWNER TO portico;

--
-- Name: seq_controller_check_list; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_check_list
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_check_list OWNER TO portico;

--
-- Name: controller_check_list; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_check_list (
    id integer DEFAULT nextval('public.seq_controller_check_list'::regclass) NOT NULL,
    control_id integer,
    status smallint NOT NULL,
    comment text,
    deadline bigint,
    original_deadline bigint,
    planned_date bigint,
    completed_date bigint,
    component_id integer,
    serie_id integer,
    location_code character varying(30),
    location_id integer,
    num_open_cases integer,
    num_pending_cases integer,
    assigned_to integer,
    billable_hours numeric(20,2)
);


ALTER TABLE public.controller_check_list OWNER TO portico;

--
-- Name: seq_controller_control; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control OWNER TO portico;

--
-- Name: controller_control; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control (
    id integer DEFAULT nextval('public.seq_controller_control'::regclass) NOT NULL,
    title character varying(100) NOT NULL,
    description text,
    start_date bigint,
    end_date bigint,
    procedure_id integer,
    requirement_id integer,
    costresponsibility_id integer,
    responsibility_id integer,
    control_area_id integer,
    repeat_type smallint,
    repeat_interval smallint,
    enabled smallint
);


ALTER TABLE public.controller_control OWNER TO portico;

--
-- Name: seq_controller_control_component_list; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_component_list
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_component_list OWNER TO portico;

--
-- Name: controller_control_component_list; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_component_list (
    id integer DEFAULT nextval('public.seq_controller_control_component_list'::regclass) NOT NULL,
    control_id integer NOT NULL,
    location_id integer NOT NULL,
    component_id integer NOT NULL,
    enabled smallint
);


ALTER TABLE public.controller_control_component_list OWNER TO portico;

--
-- Name: seq_controller_control_group; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_group
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_group OWNER TO portico;

--
-- Name: controller_control_group; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_group (
    id integer DEFAULT nextval('public.seq_controller_control_group'::regclass) NOT NULL,
    group_name character varying(255) NOT NULL,
    procedure_id integer,
    control_area_id integer,
    building_part_id character varying(30),
    component_location_id integer,
    component_criteria text
);


ALTER TABLE public.controller_control_group OWNER TO portico;

--
-- Name: seq_controller_control_group_component_list; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_group_component_list
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_group_component_list OWNER TO portico;

--
-- Name: controller_control_group_component_list; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_group_component_list (
    id integer DEFAULT nextval('public.seq_controller_control_group_component_list'::regclass) NOT NULL,
    control_group_id integer NOT NULL,
    location_id integer NOT NULL
);


ALTER TABLE public.controller_control_group_component_list OWNER TO portico;

--
-- Name: seq_controller_control_group_list; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_group_list
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_group_list OWNER TO portico;

--
-- Name: controller_control_group_list; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_group_list (
    id integer DEFAULT nextval('public.seq_controller_control_group_list'::regclass) NOT NULL,
    control_id integer NOT NULL,
    control_group_id integer NOT NULL,
    order_nr integer
);


ALTER TABLE public.controller_control_group_list OWNER TO portico;

--
-- Name: seq_controller_control_item; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_item
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_item OWNER TO portico;

--
-- Name: controller_control_item; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_item (
    id integer DEFAULT nextval('public.seq_controller_control_item'::regclass) NOT NULL,
    title character varying(255) NOT NULL,
    required boolean DEFAULT false,
    what_to_do text NOT NULL,
    how_to_do text NOT NULL,
    control_group_id integer,
    control_area_id integer,
    type character varying(255)
);


ALTER TABLE public.controller_control_item OWNER TO portico;

--
-- Name: seq_controller_control_item_list; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_item_list
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_item_list OWNER TO portico;

--
-- Name: controller_control_item_list; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_item_list (
    id integer DEFAULT nextval('public.seq_controller_control_item_list'::regclass) NOT NULL,
    control_id integer,
    control_item_id integer,
    order_nr integer
);


ALTER TABLE public.controller_control_item_list OWNER TO portico;

--
-- Name: seq_controller_control_item_option; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_item_option
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_item_option OWNER TO portico;

--
-- Name: controller_control_item_option; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_item_option (
    id integer DEFAULT nextval('public.seq_controller_control_item_option'::regclass) NOT NULL,
    option_value character varying(255) NOT NULL,
    control_item_id integer
);


ALTER TABLE public.controller_control_item_option OWNER TO portico;

--
-- Name: seq_controller_control_location_list; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_location_list
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_location_list OWNER TO portico;

--
-- Name: controller_control_location_list; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_location_list (
    id integer DEFAULT nextval('public.seq_controller_control_location_list'::regclass) NOT NULL,
    control_id integer NOT NULL,
    location_code character varying(30) NOT NULL
);


ALTER TABLE public.controller_control_location_list OWNER TO portico;

--
-- Name: seq_controller_control_serie; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_serie
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_serie OWNER TO portico;

--
-- Name: controller_control_serie; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_serie (
    id integer DEFAULT nextval('public.seq_controller_control_serie'::regclass) NOT NULL,
    control_relation_id integer NOT NULL,
    control_relation_type character varying(10) NOT NULL,
    assigned_to integer,
    start_date bigint,
    repeat_type smallint,
    repeat_interval integer,
    service_time numeric(20,2) DEFAULT 0.00,
    controle_time numeric(20,2) DEFAULT 0.00,
    enabled smallint DEFAULT 1
);


ALTER TABLE public.controller_control_serie OWNER TO portico;

--
-- Name: seq_controller_control_serie_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_control_serie_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_control_serie_history OWNER TO portico;

--
-- Name: controller_control_serie_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_control_serie_history (
    id integer DEFAULT nextval('public.seq_controller_control_serie_history'::regclass) NOT NULL,
    serie_id integer NOT NULL,
    assigned_to integer NOT NULL,
    assigned_date bigint NOT NULL
);


ALTER TABLE public.controller_control_serie_history OWNER TO portico;

--
-- Name: seq_controller_document; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_document
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_document OWNER TO portico;

--
-- Name: controller_document; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_document (
    id integer DEFAULT nextval('public.seq_controller_document'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    procedure_id integer,
    title character varying(255),
    description text,
    type_id integer NOT NULL
);


ALTER TABLE public.controller_document OWNER TO portico;

--
-- Name: seq_controller_document_types; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_document_types
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_document_types OWNER TO portico;

--
-- Name: controller_document_types; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_document_types (
    id integer DEFAULT nextval('public.seq_controller_document_types'::regclass) NOT NULL,
    title character varying(255) NOT NULL
);


ALTER TABLE public.controller_document_types OWNER TO portico;

--
-- Name: seq_controller_procedure; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_controller_procedure
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_controller_procedure OWNER TO portico;

--
-- Name: controller_procedure; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.controller_procedure (
    id integer DEFAULT nextval('public.seq_controller_procedure'::regclass) NOT NULL,
    title character varying(255) NOT NULL,
    purpose text,
    responsibility text,
    description text,
    reference text,
    attachment character varying(255),
    start_date bigint,
    end_date bigint,
    procedure_id integer,
    revision_no integer,
    revision_date bigint,
    control_area_id integer,
    modified_date bigint,
    modified_by integer
);


ALTER TABLE public.controller_procedure OWNER TO portico;

--
-- Name: seq_fm_action_pending; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_action_pending
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_action_pending OWNER TO portico;

--
-- Name: fm_action_pending; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_action_pending (
    id integer DEFAULT nextval('public.seq_fm_action_pending'::regclass) NOT NULL,
    item_id bigint NOT NULL,
    location_id integer NOT NULL,
    responsible integer NOT NULL,
    responsible_type character varying(20) NOT NULL,
    action_category integer NOT NULL,
    action_requested integer,
    action_deadline integer,
    action_performed integer,
    reminder integer DEFAULT 1,
    created_on integer NOT NULL,
    created_by integer NOT NULL,
    expired_on integer,
    expired_by integer,
    remark text
);


ALTER TABLE public.fm_action_pending OWNER TO portico;

--
-- Name: seq_fm_action_pending_category; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_action_pending_category
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_action_pending_category OWNER TO portico;

--
-- Name: fm_action_pending_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_action_pending_category (
    id integer DEFAULT nextval('public.seq_fm_action_pending_category'::regclass) NOT NULL,
    num character varying(25),
    name character varying(50),
    descr text
);


ALTER TABLE public.fm_action_pending_category OWNER TO portico;

--
-- Name: fm_activities; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_activities (
    id integer NOT NULL,
    num character varying(25) NOT NULL,
    base_descr text,
    unit integer,
    ns3420 character varying(15),
    remarkreq character varying(5) DEFAULT 'N'::character varying,
    minperae integer DEFAULT 0,
    billperae numeric(20,2) DEFAULT 0.00,
    dim_d integer,
    descr text,
    branch_id integer,
    agreement_group_id integer
);


ALTER TABLE public.fm_activities OWNER TO portico;

--
-- Name: fm_activity_price_index; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_activity_price_index (
    activity_id integer NOT NULL,
    agreement_id integer NOT NULL,
    index_count integer NOT NULL,
    current_index smallint,
    this_index numeric(20,4) DEFAULT 0.00,
    m_cost numeric(20,2) DEFAULT 0.00,
    w_cost numeric(20,2) DEFAULT 0.00,
    total_cost numeric(20,2) DEFAULT 0.00,
    entry_date integer,
    index_date integer,
    user_id integer
);


ALTER TABLE public.fm_activity_price_index OWNER TO portico;

--
-- Name: fm_agreement; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_agreement (
    group_id integer NOT NULL,
    id integer NOT NULL,
    vendor_id integer NOT NULL,
    contract_id character varying(30),
    name character varying(100) NOT NULL,
    descr text,
    status character varying(10),
    entry_date integer,
    start_date integer,
    end_date integer,
    termination_date integer,
    category integer,
    user_id integer
);


ALTER TABLE public.fm_agreement OWNER TO portico;

--
-- Name: fm_agreement_group; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_agreement_group (
    id integer NOT NULL,
    num character varying(25) NOT NULL,
    descr character varying(50) NOT NULL,
    status character varying(15) NOT NULL
);


ALTER TABLE public.fm_agreement_group OWNER TO portico;

--
-- Name: fm_agreement_status; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_agreement_status (
    id character varying(20) NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_agreement_status OWNER TO portico;

--
-- Name: fm_async_method; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_async_method (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    data text,
    descr text
);


ALTER TABLE public.fm_async_method OWNER TO portico;

--
-- Name: fm_authorities_demands; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_authorities_demands (
    id integer NOT NULL,
    name character varying(200) NOT NULL,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_authorities_demands OWNER TO portico;

--
-- Name: fm_b_account; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_b_account (
    id character varying(20) NOT NULL,
    category integer NOT NULL,
    descr character varying(100) NOT NULL,
    mva integer,
    responsible integer,
    active smallint DEFAULT 0,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_b_account OWNER TO portico;

--
-- Name: fm_b_account_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_b_account_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL,
    active smallint DEFAULT 0,
    external_project smallint DEFAULT 0
);


ALTER TABLE public.fm_b_account_category OWNER TO portico;

--
-- Name: fm_branch; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_branch (
    id integer NOT NULL,
    num character varying(20) NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_branch OWNER TO portico;

--
-- Name: fm_budget; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_budget (
    id integer NOT NULL,
    year integer NOT NULL,
    b_account_id character varying(20) NOT NULL,
    district_id integer,
    revision integer NOT NULL,
    access character varying(7),
    user_id integer,
    entry_date integer,
    budget_cost integer DEFAULT 0,
    remark text,
    ecodimb integer,
    category integer
);


ALTER TABLE public.fm_budget OWNER TO portico;

--
-- Name: fm_budget_basis; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_budget_basis (
    id integer NOT NULL,
    year integer NOT NULL,
    b_group character varying(4) NOT NULL,
    district_id integer NOT NULL,
    revision integer NOT NULL,
    access character varying(7),
    user_id integer,
    entry_date integer,
    budget_cost integer DEFAULT 0,
    remark text,
    distribute_year text,
    ecodimb integer,
    category integer
);


ALTER TABLE public.fm_budget_basis OWNER TO portico;

--
-- Name: seq_fm_budget_cost; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_budget_cost
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_budget_cost OWNER TO portico;

--
-- Name: fm_budget_cost; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_budget_cost (
    id integer DEFAULT nextval('public.seq_fm_budget_cost'::regclass) NOT NULL,
    year integer NOT NULL,
    month integer NOT NULL,
    b_account_id character varying(20) NOT NULL,
    amount numeric(20,2) DEFAULT 0
);


ALTER TABLE public.fm_budget_cost OWNER TO portico;

--
-- Name: fm_budget_period; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_budget_period (
    year integer NOT NULL,
    month integer NOT NULL,
    b_account_id character varying(4) NOT NULL,
    percent_ integer DEFAULT 0,
    user_id integer,
    entry_date integer,
    remark text
);


ALTER TABLE public.fm_budget_period OWNER TO portico;

--
-- Name: fm_building_part; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_building_part (
    id character varying(5) NOT NULL,
    descr character varying(50),
    filter_1 smallint,
    filter_2 smallint,
    filter_3 smallint,
    filter_4 smallint
);


ALTER TABLE public.fm_building_part OWNER TO portico;

--
-- Name: fm_cache; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_cache (
    name character varying(50) NOT NULL,
    value text
);


ALTER TABLE public.fm_cache OWNER TO portico;

--
-- Name: fm_chapter; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_chapter (
    id integer NOT NULL,
    descr character varying(50) NOT NULL
);


ALTER TABLE public.fm_chapter OWNER TO portico;

--
-- Name: fm_condition_survey; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_condition_survey (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    p_num character varying(15),
    p_entity_id integer,
    p_cat_id integer,
    location_code character varying(20),
    loc1 character varying(6),
    loc2 character varying(4),
    loc3 character varying(4),
    loc4 character varying(4),
    descr text,
    address character varying(255),
    status_id integer NOT NULL,
    category integer,
    coordinator_id integer,
    vendor_id integer,
    report_date integer,
    user_id integer,
    entry_date integer,
    modified_date integer,
    multiplier numeric(20,2) DEFAULT 1
);


ALTER TABLE public.fm_condition_survey OWNER TO portico;

--
-- Name: seq_fm_condition_survey_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_condition_survey_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_condition_survey_history OWNER TO portico;

--
-- Name: fm_condition_survey_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_condition_survey_history (
    history_id integer DEFAULT nextval('public.seq_fm_condition_survey_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_appname character varying(64) NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.fm_condition_survey_history OWNER TO portico;

--
-- Name: fm_condition_survey_status; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_condition_survey_status (
    id integer NOT NULL,
    descr character varying(255) NOT NULL,
    closed smallint,
    in_progress smallint,
    delivered smallint,
    sorting integer
);


ALTER TABLE public.fm_condition_survey_status OWNER TO portico;

--
-- Name: seq_fm_cron_log; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_cron_log
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_cron_log OWNER TO portico;

--
-- Name: fm_cron_log; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_cron_log (
    id integer DEFAULT nextval('public.seq_fm_cron_log'::regclass) NOT NULL,
    cron smallint,
    cron_date timestamp without time zone DEFAULT now() NOT NULL,
    process character varying(255) NOT NULL,
    message text
);


ALTER TABLE public.fm_cron_log OWNER TO portico;

--
-- Name: fm_custom; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_custom (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    sql_text text NOT NULL,
    entry_date integer,
    user_id integer
);


ALTER TABLE public.fm_custom OWNER TO portico;

--
-- Name: fm_custom_cols; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_custom_cols (
    custom_id integer NOT NULL,
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    descr character varying(50) NOT NULL,
    sorting integer NOT NULL
);


ALTER TABLE public.fm_custom_cols OWNER TO portico;

--
-- Name: seq_fm_custom_menu_items; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_custom_menu_items
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_custom_menu_items OWNER TO portico;

--
-- Name: fm_custom_menu_items; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_custom_menu_items (
    id integer DEFAULT nextval('public.seq_fm_custom_menu_items'::regclass) NOT NULL,
    parent_id integer,
    text character varying(200) NOT NULL,
    url text,
    target character varying(15),
    location character varying(200) NOT NULL,
    local_files smallint,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_custom_menu_items OWNER TO portico;

--
-- Name: fm_district; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_district (
    id smallint NOT NULL,
    descr character varying(50),
    delivery_address text
);


ALTER TABLE public.fm_district OWNER TO portico;

--
-- Name: seq_fm_document; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_document
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_document OWNER TO portico;

--
-- Name: fm_document; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_document (
    id integer DEFAULT nextval('public.seq_fm_document'::regclass) NOT NULL,
    title character varying(100),
    document_name character varying(50),
    link text,
    descr text,
    version character varying(10),
    document_date integer,
    entry_date integer,
    status character varying(10),
    p_num character varying(15),
    p_entity_id integer,
    p_cat_id integer,
    location_code character varying(20) NOT NULL,
    loc1 character varying(6),
    loc2 character varying(4),
    loc3 character varying(4),
    loc4 character varying(4),
    address character varying(150),
    coordinator integer,
    vendor_id integer,
    branch_id integer,
    category integer,
    user_id integer,
    access character varying(7)
);


ALTER TABLE public.fm_document OWNER TO portico;

--
-- Name: seq_fm_document_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_document_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_document_history OWNER TO portico;

--
-- Name: fm_document_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_document_history (
    history_id integer DEFAULT nextval('public.seq_fm_document_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_appname character varying(64) NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.fm_document_history OWNER TO portico;

--
-- Name: seq_fm_document_relation; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_document_relation
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_document_relation OWNER TO portico;

--
-- Name: fm_document_relation; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_document_relation (
    id integer DEFAULT nextval('public.seq_fm_document_relation'::regclass) NOT NULL,
    document_id integer NOT NULL,
    location_id integer NOT NULL,
    location_item_id integer NOT NULL,
    entry_date integer
);


ALTER TABLE public.fm_document_relation OWNER TO portico;

--
-- Name: fm_document_status; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_document_status (
    id character varying(20) NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_document_status OWNER TO portico;

--
-- Name: seq_fm_eco_period_transition; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_eco_period_transition
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_eco_period_transition OWNER TO portico;

--
-- Name: fm_eco_period_transition; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_eco_period_transition (
    id integer DEFAULT nextval('public.seq_fm_eco_period_transition'::regclass) NOT NULL,
    month integer NOT NULL,
    day integer,
    hour integer,
    remark character varying(60),
    user_id integer NOT NULL,
    entry_date integer NOT NULL,
    modified_date integer
);


ALTER TABLE public.fm_eco_period_transition OWNER TO portico;

--
-- Name: fm_eco_periodization; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_eco_periodization (
    id integer NOT NULL,
    descr character varying(64) NOT NULL,
    active smallint DEFAULT 0
);


ALTER TABLE public.fm_eco_periodization OWNER TO portico;

--
-- Name: seq_fm_eco_periodization_outline; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_eco_periodization_outline
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_eco_periodization_outline OWNER TO portico;

--
-- Name: fm_eco_periodization_outline; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_eco_periodization_outline (
    id integer DEFAULT nextval('public.seq_fm_eco_periodization_outline'::regclass) NOT NULL,
    periodization_id integer NOT NULL,
    month integer,
    value numeric(20,6) DEFAULT 0.000000 NOT NULL,
    dividend integer,
    divisor integer,
    remark character varying(60) NOT NULL
);


ALTER TABLE public.fm_eco_periodization_outline OWNER TO portico;

--
-- Name: fm_eco_service; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_eco_service (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    active smallint DEFAULT 1
);


ALTER TABLE public.fm_eco_service OWNER TO portico;

--
-- Name: fm_ecoart; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecoart (
    id integer NOT NULL,
    descr character varying(25) NOT NULL
);


ALTER TABLE public.fm_ecoart OWNER TO portico;

--
-- Name: fm_ecoavvik; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecoavvik (
    bilagsnr integer NOT NULL,
    belop numeric(20,2) DEFAULT 0 NOT NULL,
    fakturadato timestamp without time zone NOT NULL,
    forfallsdato timestamp without time zone NOT NULL,
    artid smallint NOT NULL,
    godkjentbelop numeric(20,2) DEFAULT 0,
    spvend_code integer,
    oppsynsmannid character varying(12),
    saksbehandlerid character varying(12),
    budsjettansvarligid character varying(12) NOT NULL,
    utbetalingid character varying(12),
    oppsynsigndato timestamp without time zone,
    saksigndato timestamp without time zone,
    budsjettsigndato timestamp without time zone,
    utbetalingsigndato timestamp without time zone,
    overftid timestamp without time zone
);


ALTER TABLE public.fm_ecoavvik OWNER TO portico;

--
-- Name: seq_fm_ecobilag; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_ecobilag
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_ecobilag OWNER TO portico;

--
-- Name: fm_ecobilag; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecobilag (
    id integer DEFAULT nextval('public.seq_fm_ecobilag'::regclass) NOT NULL,
    bilagsnr integer NOT NULL,
    bilagsnr_ut integer NOT NULL,
    kidnr character varying(30),
    typeid smallint NOT NULL,
    kildeid smallint NOT NULL,
    project_id integer,
    kostra_id integer,
    pmwrkord_code integer,
    belop numeric(20,2) DEFAULT 0 NOT NULL,
    fakturadato timestamp without time zone NOT NULL,
    periode integer,
    forfallsdato timestamp without time zone NOT NULL,
    fakturanr character varying(15) NOT NULL,
    spbudact_code character varying(30),
    regtid timestamp without time zone NOT NULL,
    artid smallint NOT NULL,
    godkjentbelop numeric(20,2) DEFAULT 0,
    spvend_code integer,
    dima character varying(20),
    loc1 character varying(10),
    dimb smallint,
    mvakode smallint,
    dimd character varying(5),
    dime integer,
    oppsynsmannid character varying(12),
    saksbehandlerid character varying(12),
    budsjettansvarligid character varying(12) NOT NULL,
    utbetalingid character varying(12),
    oppsynsigndato timestamp without time zone,
    saksigndato timestamp without time zone,
    budsjettsigndato timestamp without time zone,
    utbetalingsigndato timestamp without time zone,
    merknad text,
    splitt integer,
    kreditnota smallint,
    pre_transfer smallint,
    item_type integer,
    item_id character varying(20),
    external_ref character varying(30),
    currency character varying(3),
    process_log text,
    process_code character varying(10),
    periodization integer,
    periodization_start integer,
    line_text character varying(255),
    external_voucher_id bigint
);


ALTER TABLE public.fm_ecobilag OWNER TO portico;

--
-- Name: fm_ecobilag_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecobilag_category (
    id smallint NOT NULL,
    descr character varying(25) NOT NULL
);


ALTER TABLE public.fm_ecobilag_category OWNER TO portico;

--
-- Name: fm_ecobilag_process_code; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecobilag_process_code (
    id character varying(10) NOT NULL,
    name character varying(200) NOT NULL,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_ecobilag_process_code OWNER TO portico;

--
-- Name: seq_fm_ecobilag_process_log; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_ecobilag_process_log
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_ecobilag_process_log OWNER TO portico;

--
-- Name: fm_ecobilag_process_log; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecobilag_process_log (
    id integer DEFAULT nextval('public.seq_fm_ecobilag_process_log'::regclass) NOT NULL,
    bilagsnr integer NOT NULL,
    process_code character varying(10),
    process_log text,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_ecobilag_process_log OWNER TO portico;

--
-- Name: fm_ecobilag_sum_view; Type: VIEW; Schema: public; Owner: portico
--

CREATE VIEW public.fm_ecobilag_sum_view AS
 SELECT DISTINCT fm_ecobilag.bilagsnr,
    sum(fm_ecobilag.godkjentbelop) AS approved_amount,
    sum(fm_ecobilag.belop) AS amount
   FROM public.fm_ecobilag
  GROUP BY fm_ecobilag.bilagsnr
  ORDER BY fm_ecobilag.bilagsnr;


ALTER TABLE public.fm_ecobilag_sum_view OWNER TO portico;

--
-- Name: fm_ecobilagkilde; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecobilagkilde (
    id smallint NOT NULL,
    name character varying(20) NOT NULL,
    description text
);


ALTER TABLE public.fm_ecobilagkilde OWNER TO portico;

--
-- Name: fm_ecobilagoverf; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecobilagoverf (
    id integer NOT NULL,
    bilagsnr integer NOT NULL,
    bilagsnr_ut integer NOT NULL,
    kidnr character varying(30),
    typeid smallint NOT NULL,
    kildeid smallint NOT NULL,
    project_id integer,
    kostra_id integer,
    pmwrkord_code integer,
    belop numeric(20,2) DEFAULT 0 NOT NULL,
    fakturadato timestamp without time zone NOT NULL,
    periode integer,
    forfallsdato timestamp without time zone NOT NULL,
    fakturanr character varying(15) NOT NULL,
    spbudact_code character varying(30),
    regtid timestamp without time zone NOT NULL,
    artid smallint NOT NULL,
    godkjentbelop numeric(20,2) DEFAULT 0,
    spvend_code integer,
    dima character varying(20),
    loc1 character varying(10),
    dimb smallint,
    mvakode smallint,
    dimd character varying(5),
    dime integer,
    oppsynsmannid character varying(12),
    saksbehandlerid character varying(12),
    budsjettansvarligid character varying(12) NOT NULL,
    utbetalingid character varying(12),
    oppsynsigndato timestamp without time zone,
    saksigndato timestamp without time zone,
    budsjettsigndato timestamp without time zone,
    utbetalingsigndato timestamp without time zone,
    overftid timestamp without time zone,
    ordrebelop numeric(20,2) DEFAULT 0 NOT NULL,
    merknad text,
    splitt integer,
    filnavn character varying(255) NOT NULL,
    kreditnota smallint,
    item_type integer,
    item_id character varying(20),
    external_ref character varying(30),
    currency character varying(3),
    process_log text,
    process_code character varying(10),
    periodization integer,
    periodization_start integer,
    manual_record smallint,
    line_text character varying(255),
    external_voucher_id bigint
);


ALTER TABLE public.fm_ecobilagoverf OWNER TO portico;

--
-- Name: fm_ecodimb; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecodimb (
    id integer NOT NULL,
    descr character varying(50) NOT NULL,
    org_unit_id integer NOT NULL,
    active smallint DEFAULT 1
);


ALTER TABLE public.fm_ecodimb OWNER TO portico;

--
-- Name: fm_ecodimb_role; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecodimb_role (
    id integer NOT NULL,
    name character varying(25) NOT NULL
);


ALTER TABLE public.fm_ecodimb_role OWNER TO portico;

--
-- Name: seq_fm_ecodimb_role_user; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_ecodimb_role_user
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_ecodimb_role_user OWNER TO portico;

--
-- Name: fm_ecodimb_role_user; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecodimb_role_user (
    id integer DEFAULT nextval('public.seq_fm_ecodimb_role_user'::regclass) NOT NULL,
    ecodimb integer NOT NULL,
    user_id integer NOT NULL,
    role_id integer NOT NULL,
    default_user smallint DEFAULT 0,
    active_from integer NOT NULL,
    active_to integer DEFAULT 0,
    created_on integer NOT NULL,
    created_by integer NOT NULL,
    expired_on integer,
    expired_by integer
);


ALTER TABLE public.fm_ecodimb_role_user OWNER TO portico;

--
-- Name: seq_fm_ecodimb_role_user_substitute; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_ecodimb_role_user_substitute
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_ecodimb_role_user_substitute OWNER TO portico;

--
-- Name: fm_ecodimb_role_user_substitute; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecodimb_role_user_substitute (
    id integer DEFAULT nextval('public.seq_fm_ecodimb_role_user_substitute'::regclass) NOT NULL,
    user_id integer NOT NULL,
    substitute_user_id integer NOT NULL
);


ALTER TABLE public.fm_ecodimb_role_user_substitute OWNER TO portico;

--
-- Name: fm_ecodimd; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecodimd (
    id character varying(5) NOT NULL,
    descr character varying(25) NOT NULL
);


ALTER TABLE public.fm_ecodimd OWNER TO portico;

--
-- Name: fm_ecologg; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecologg (
    batchid integer NOT NULL,
    ecobilagid integer,
    status smallint,
    melding character varying(255),
    tid timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_ecologg OWNER TO portico;

--
-- Name: fm_ecomva; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecomva (
    id integer NOT NULL,
    percent_ integer,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_ecomva OWNER TO portico;

--
-- Name: fm_ecouser; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ecouser (
    id integer NOT NULL,
    lid character varying(25) NOT NULL,
    initials character varying(6)
);


ALTER TABLE public.fm_ecouser OWNER TO portico;

--
-- Name: fm_entity; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_entity (
    location_id integer NOT NULL,
    id integer NOT NULL,
    name character varying(20) NOT NULL,
    descr character varying(50),
    location_form integer,
    documentation integer,
    lookup_entity text
);


ALTER TABLE public.fm_entity OWNER TO portico;

--
-- Name: fm_entity_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_entity_category (
    location_id integer NOT NULL,
    entity_id integer NOT NULL,
    id integer NOT NULL,
    name character varying(100),
    descr text,
    prefix character varying(50),
    lookup_tenant integer,
    tracking integer,
    location_level integer,
    location_link_level integer,
    fileupload integer,
    loc_link integer,
    start_project integer,
    start_ticket smallint,
    is_eav smallint,
    enable_bulk smallint,
    enable_controller smallint,
    jasperupload smallint,
    parent_id integer,
    level integer,
    org_unit smallint,
    entity_group_id integer
);


ALTER TABLE public.fm_entity_category OWNER TO portico;

--
-- Name: seq_fm_entity_group; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_entity_group
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_entity_group OWNER TO portico;

--
-- Name: fm_entity_group; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_entity_group (
    id integer DEFAULT nextval('public.seq_fm_entity_group'::regclass) NOT NULL,
    name character varying(100) NOT NULL,
    descr text,
    active smallint DEFAULT 0,
    user_id integer NOT NULL,
    entry_date bigint NOT NULL,
    modified_date bigint NOT NULL
);


ALTER TABLE public.fm_entity_group OWNER TO portico;

--
-- Name: seq_fm_entity_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_entity_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_entity_history OWNER TO portico;

--
-- Name: fm_entity_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_entity_history (
    history_id integer DEFAULT nextval('public.seq_fm_entity_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_appname character varying(64) NOT NULL,
    history_attrib_id integer NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.fm_entity_history OWNER TO portico;

--
-- Name: fm_entity_lookup; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_entity_lookup (
    entity_id integer NOT NULL,
    location character varying(15) NOT NULL,
    type character varying(15) NOT NULL
);


ALTER TABLE public.fm_entity_lookup OWNER TO portico;

--
-- Name: seq_fm_event; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_event
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_event OWNER TO portico;

--
-- Name: fm_event; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_event (
    id integer DEFAULT nextval('public.seq_fm_event'::regclass) NOT NULL,
    location_id integer NOT NULL,
    location_item_id integer NOT NULL,
    attrib_id character varying(50) DEFAULT 0,
    responsible_id integer,
    action_id integer,
    descr text,
    start_date integer NOT NULL,
    end_date integer,
    repeat_type integer,
    repeat_day integer,
    repeat_interval integer,
    enabled smallint,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_event OWNER TO portico;

--
-- Name: fm_event_action; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_event_action (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    action character varying(100) NOT NULL,
    data text,
    descr text,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_event_action OWNER TO portico;

--
-- Name: fm_event_exception; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_event_exception (
    event_id integer NOT NULL,
    exception_time integer NOT NULL,
    descr text,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_event_exception OWNER TO portico;

--
-- Name: fm_event_receipt; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_event_receipt (
    event_id integer NOT NULL,
    receipt_time integer NOT NULL,
    descr text,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_event_receipt OWNER TO portico;

--
-- Name: fm_event_schedule; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_event_schedule (
    event_id integer NOT NULL,
    schedule_time integer NOT NULL,
    descr text,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_event_schedule OWNER TO portico;

--
-- Name: fm_external_project; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_external_project (
    id character varying(10) NOT NULL,
    name character varying(255) NOT NULL,
    budget integer,
    active smallint DEFAULT 1
);


ALTER TABLE public.fm_external_project OWNER TO portico;

--
-- Name: fm_gab_location; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_gab_location (
    location_code character varying(20) NOT NULL,
    gab_id character varying(20) NOT NULL,
    user_id integer,
    entry_date integer,
    loc1 character varying(6),
    loc2 character varying(4),
    loc3 character varying(4),
    loc4 character varying(4),
    address character varying(150),
    split smallint,
    remark character varying(50),
    owner character varying(5),
    spredning integer
);


ALTER TABLE public.fm_gab_location OWNER TO portico;

--
-- Name: seq_fm_generic_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_generic_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_generic_history OWNER TO portico;

--
-- Name: fm_generic_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_generic_history (
    history_id integer DEFAULT nextval('public.seq_fm_generic_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL,
    history_attrib_id integer NOT NULL,
    location_id integer NOT NULL,
    app_id integer NOT NULL
);


ALTER TABLE public.fm_generic_history OWNER TO portico;

--
-- Name: fm_idgenerator; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_idgenerator (
    name character varying(30) NOT NULL,
    start_date integer DEFAULT 0 NOT NULL,
    value integer NOT NULL,
    increment integer,
    descr character varying(30)
);


ALTER TABLE public.fm_idgenerator OWNER TO portico;

--
-- Name: fm_investment; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_investment (
    entity_id character varying(20) NOT NULL,
    invest_id integer NOT NULL,
    entity_type character varying(20),
    p_num character varying(15),
    p_entity_id integer,
    p_cat_id integer,
    location_code character varying(20),
    loc1 character varying(6),
    loc2 character varying(4),
    loc3 character varying(4),
    loc4 character varying(4),
    address character varying(150),
    descr character varying(255),
    writeoff_year integer
);


ALTER TABLE public.fm_investment OWNER TO portico;

--
-- Name: fm_investment_value; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_investment_value (
    entity_id character varying(20) NOT NULL,
    invest_id integer NOT NULL,
    index_count integer NOT NULL,
    current_index smallint,
    this_index numeric(20,4) DEFAULT 0,
    initial_value numeric(20,2) DEFAULT 0,
    value numeric(20,2) DEFAULT 0,
    index_date timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_investment_value OWNER TO portico;

--
-- Name: seq_fm_jasper; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_jasper
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_jasper OWNER TO portico;

--
-- Name: fm_jasper; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_jasper (
    id integer DEFAULT nextval('public.seq_fm_jasper'::regclass) NOT NULL,
    location_id integer NOT NULL,
    title character varying(100),
    descr character varying(255),
    formats character varying(255),
    version character varying(10),
    access character varying(7),
    user_id integer,
    entry_date integer,
    modified_by integer,
    modified_date integer
);


ALTER TABLE public.fm_jasper OWNER TO portico;

--
-- Name: fm_jasper_format_type; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_jasper_format_type (
    id character varying(20) NOT NULL
);


ALTER TABLE public.fm_jasper_format_type OWNER TO portico;

--
-- Name: seq_fm_jasper_input; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_jasper_input
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_jasper_input OWNER TO portico;

--
-- Name: fm_jasper_input; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_jasper_input (
    id integer DEFAULT nextval('public.seq_fm_jasper_input'::regclass) NOT NULL,
    jasper_id integer NOT NULL,
    input_type_id integer NOT NULL,
    is_id smallint,
    name character varying(50) NOT NULL,
    descr character varying(255)
);


ALTER TABLE public.fm_jasper_input OWNER TO portico;

--
-- Name: seq_fm_jasper_input_type; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_jasper_input_type
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_jasper_input_type OWNER TO portico;

--
-- Name: fm_jasper_input_type; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_jasper_input_type (
    id integer DEFAULT nextval('public.seq_fm_jasper_input_type'::regclass) NOT NULL,
    name character varying(20) NOT NULL,
    descr character varying(255)
);


ALTER TABLE public.fm_jasper_input_type OWNER TO portico;

--
-- Name: fm_key_loc; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_key_loc (
    id integer NOT NULL,
    num character varying(20) NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_key_loc OWNER TO portico;

--
-- Name: fm_location1; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location1 (
    id integer,
    location_code character varying(16) NOT NULL,
    loc1 character varying(6) NOT NULL,
    loc1_name character varying(50),
    part_of_town_id integer,
    entry_date integer,
    category integer,
    user_id integer,
    owner_id integer,
    status integer,
    mva integer,
    remark text,
    kostra_id integer,
    change_type integer,
    rental_area numeric(20,2) DEFAULT 0.00,
    area_gross numeric(20,2) DEFAULT 0.00,
    area_net numeric(20,2) DEFAULT 0.00,
    area_usable numeric(20,2) DEFAULT 0.00,
    delivery_address text,
    modified_by integer,
    modified_on timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_location1 OWNER TO portico;

--
-- Name: fm_location1_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location1_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_location1_category OWNER TO portico;

--
-- Name: fm_location1_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location1_history (
    id integer,
    location_code character varying(16) NOT NULL,
    loc1 character varying(6) NOT NULL,
    loc1_name character varying(50),
    part_of_town_id integer,
    entry_date integer,
    category integer,
    user_id integer,
    owner_id integer,
    status integer,
    mva integer,
    remark text,
    kostra_id integer,
    change_type integer,
    rental_area numeric(20,2) DEFAULT 0.00,
    area_gross numeric(20,2) DEFAULT 0.00,
    area_net numeric(20,2) DEFAULT 0.00,
    area_usable numeric(20,2) DEFAULT 0.00,
    delivery_address text,
    exp_date timestamp without time zone DEFAULT now(),
    modified_by integer,
    modified_on timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_location1_history OWNER TO portico;

--
-- Name: fm_location2; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location2 (
    id integer,
    location_code character varying(50) NOT NULL,
    loc1 character varying(6) NOT NULL,
    loc2 character varying(4) NOT NULL,
    loc2_name character varying(50),
    entry_date integer,
    category integer,
    user_id integer,
    status integer,
    remark text,
    change_type integer,
    rental_area numeric(20,2) DEFAULT 0.00,
    area_gross numeric(20,2) DEFAULT 0.00,
    area_net numeric(20,2) DEFAULT 0.00,
    area_usable numeric(20,2) DEFAULT 0.00,
    modified_by integer,
    modified_on timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_location2 OWNER TO portico;

--
-- Name: fm_location2_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location2_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_location2_category OWNER TO portico;

--
-- Name: fm_location2_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location2_history (
    id integer,
    location_code character varying(50) NOT NULL,
    loc1 character varying(6) NOT NULL,
    loc2 character varying(4) NOT NULL,
    loc2_name character varying(50),
    entry_date integer,
    category integer,
    user_id integer,
    status integer,
    remark text,
    change_type integer,
    rental_area numeric(20,2) DEFAULT 0.00,
    area_gross numeric(20,2) DEFAULT 0.00,
    area_net numeric(20,2) DEFAULT 0.00,
    area_usable numeric(20,2) DEFAULT 0.00,
    exp_date timestamp without time zone DEFAULT now(),
    modified_by integer,
    modified_on timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_location2_history OWNER TO portico;

--
-- Name: fm_location3; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location3 (
    id integer,
    location_code character varying(50) NOT NULL,
    loc1 character varying(6) NOT NULL,
    loc2 character varying(4) NOT NULL,
    loc3 character varying(4) NOT NULL,
    loc3_name character varying(50),
    entry_date integer,
    category integer,
    user_id integer,
    status integer,
    remark text,
    change_type integer,
    rental_area numeric(20,2) DEFAULT 0.00,
    area_gross numeric(20,2) DEFAULT 0.00,
    area_net numeric(20,2) DEFAULT 0.00,
    area_usable numeric(20,2) DEFAULT 0.00,
    modified_by integer,
    modified_on timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_location3 OWNER TO portico;

--
-- Name: fm_location3_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location3_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_location3_category OWNER TO portico;

--
-- Name: fm_location3_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location3_history (
    id integer,
    location_code character varying(50) NOT NULL,
    loc1 character varying(6) NOT NULL,
    loc2 character varying(4) NOT NULL,
    loc3 character varying(4) NOT NULL,
    loc3_name character varying(50),
    entry_date integer,
    category integer,
    user_id integer,
    status integer,
    remark text,
    change_type integer,
    rental_area numeric(20,2) DEFAULT 0.00,
    area_gross numeric(20,2) DEFAULT 0.00,
    area_net numeric(20,2) DEFAULT 0.00,
    area_usable numeric(20,2) DEFAULT 0.00,
    exp_date timestamp without time zone DEFAULT now(),
    modified_by integer,
    modified_on timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_location3_history OWNER TO portico;

--
-- Name: fm_location4; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location4 (
    id integer,
    location_code character varying(50) NOT NULL,
    loc1 character varying(6) NOT NULL,
    loc2 character varying(4) NOT NULL,
    loc3 character varying(4) NOT NULL,
    loc4 character varying(4) NOT NULL,
    loc4_name character varying(50),
    entry_date integer,
    category integer,
    street_id integer,
    street_number character varying(10),
    user_id integer,
    tenant_id integer,
    status integer,
    remark text,
    change_type integer,
    rental_area numeric(20,2) DEFAULT 0.00,
    area_gross numeric(20,2) DEFAULT 0.00,
    area_net numeric(20,2) DEFAULT 0.00,
    area_usable numeric(20,2) DEFAULT 0.00,
    modified_by integer,
    modified_on timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_location4 OWNER TO portico;

--
-- Name: fm_location4_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location4_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_location4_category OWNER TO portico;

--
-- Name: fm_location4_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location4_history (
    id integer,
    location_code character varying(50) NOT NULL,
    loc1 character varying(6) NOT NULL,
    loc2 character varying(4) NOT NULL,
    loc3 character varying(4) NOT NULL,
    loc4 character varying(4) NOT NULL,
    loc4_name character varying(50),
    entry_date integer,
    category integer,
    street_id integer,
    street_number character varying(10),
    user_id integer,
    tenant_id integer,
    status integer,
    remark text,
    change_type integer,
    rental_area numeric(20,2) DEFAULT 0.00,
    area_gross numeric(20,2) DEFAULT 0.00,
    area_net numeric(20,2) DEFAULT 0.00,
    area_usable numeric(20,2) DEFAULT 0.00,
    exp_date timestamp without time zone DEFAULT now(),
    modified_by integer,
    modified_on timestamp without time zone DEFAULT now()
);


ALTER TABLE public.fm_location4_history OWNER TO portico;

--
-- Name: fm_location_config; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location_config (
    column_name character varying(20) NOT NULL,
    location_type integer NOT NULL,
    input_text character varying(50),
    lookup_form smallint,
    f_key smallint,
    ref_to_category smallint,
    query_value smallint,
    reference_table character varying(30),
    reference_id character varying(15),
    datatype character varying(10),
    precision_ integer,
    scale integer,
    default_value character varying(20),
    nullable character varying(5) DEFAULT true NOT NULL
);


ALTER TABLE public.fm_location_config OWNER TO portico;

--
-- Name: seq_fm_location_contact; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_location_contact
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_location_contact OWNER TO portico;

--
-- Name: fm_location_contact; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location_contact (
    id integer DEFAULT nextval('public.seq_fm_location_contact'::regclass) NOT NULL,
    contact_id integer NOT NULL,
    location_code character varying(20) NOT NULL,
    user_id integer NOT NULL,
    entry_date integer NOT NULL,
    modified_date integer NOT NULL
);


ALTER TABLE public.fm_location_contact OWNER TO portico;

--
-- Name: seq_fm_location_exception; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_location_exception
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_location_exception OWNER TO portico;

--
-- Name: fm_location_exception; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location_exception (
    id integer DEFAULT nextval('public.seq_fm_location_exception'::regclass) NOT NULL,
    location_code character varying(20) NOT NULL,
    severity_id integer NOT NULL,
    category_id integer NOT NULL,
    category_text_id integer,
    descr text,
    start_date bigint NOT NULL,
    end_date bigint,
    reference text,
    alert_vendor smallint,
    user_id integer NOT NULL,
    entry_date integer NOT NULL,
    modified_date integer NOT NULL
);


ALTER TABLE public.fm_location_exception OWNER TO portico;

--
-- Name: seq_fm_location_exception_category; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_location_exception_category
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_location_exception_category OWNER TO portico;

--
-- Name: fm_location_exception_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location_exception_category (
    id integer DEFAULT nextval('public.seq_fm_location_exception_category'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    parent_id integer
);


ALTER TABLE public.fm_location_exception_category OWNER TO portico;

--
-- Name: seq_fm_location_exception_category_text; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_location_exception_category_text
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_location_exception_category_text OWNER TO portico;

--
-- Name: fm_location_exception_category_text; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location_exception_category_text (
    id integer DEFAULT nextval('public.seq_fm_location_exception_category_text'::regclass) NOT NULL,
    category_id integer NOT NULL,
    content text
);


ALTER TABLE public.fm_location_exception_category_text OWNER TO portico;

--
-- Name: fm_location_exception_severity; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location_exception_severity (
    id integer NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.fm_location_exception_severity OWNER TO portico;

--
-- Name: fm_location_type; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_location_type (
    id integer NOT NULL,
    name character varying(20),
    descr character varying(50),
    pk text,
    ix text,
    uc text,
    list_info character varying(255),
    list_address smallint,
    list_documents smallint,
    enable_controller smallint
);


ALTER TABLE public.fm_location_type OWNER TO portico;

--
-- Name: seq_fm_locations; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_locations
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_locations OWNER TO portico;

--
-- Name: fm_locations; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_locations (
    id integer DEFAULT nextval('public.seq_fm_locations'::regclass) NOT NULL,
    level integer NOT NULL,
    location_code character varying(50) NOT NULL,
    loc1 character varying(6) NOT NULL,
    name text
);


ALTER TABLE public.fm_locations OWNER TO portico;

--
-- Name: fm_ns3420; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_ns3420 (
    id integer NOT NULL,
    num character varying(20) NOT NULL,
    parent_id integer,
    enhet character varying(6),
    tekst1 character varying(50),
    tekst2 character varying(50),
    tekst3 character varying(50),
    tekst4 character varying(50),
    tekst5 character varying(50),
    tekst6 character varying(50),
    type character varying(20)
);


ALTER TABLE public.fm_ns3420 OWNER TO portico;

--
-- Name: fm_workorder; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_workorder (
    id bigint NOT NULL,
    num character varying(20) NOT NULL,
    project_id integer NOT NULL,
    user_id integer NOT NULL,
    access character varying(7),
    category integer,
    chapter_id integer,
    entry_date integer NOT NULL,
    start_date bigint NOT NULL,
    end_date bigint,
    tender_deadline bigint,
    tender_received bigint,
    inspection_on_completion bigint,
    coordinator integer,
    vendor_id integer,
    status character varying(20) DEFAULT 'active'::character varying NOT NULL,
    descr text,
    title character varying(255) NOT NULL,
    budget numeric(20,2) DEFAULT 0.00,
    calculation numeric(20,2) DEFAULT 0.00,
    combined_cost numeric(20,2) DEFAULT 0.00,
    deviation numeric(20,2),
    act_mtrl_cost numeric(20,2) DEFAULT 0.00,
    act_vendor_cost numeric(20,2) DEFAULT 0.00,
    actual_cost numeric(20,2) DEFAULT 0.00,
    addition integer,
    rig_addition integer,
    account_id character varying(20),
    key_fetch integer,
    key_deliver integer,
    integration integer,
    charge_tenant smallint,
    claim_issued smallint,
    paid smallint DEFAULT 1,
    ecodimb integer,
    p_num character varying(15),
    p_entity_id integer,
    p_cat_id integer,
    location_code character varying(20),
    address character varying(150),
    tenant_id integer,
    contact_phone character varying(20),
    paid_percent integer DEFAULT 0,
    event_id integer,
    billable_hours numeric(20,2),
    contract_sum numeric(20,2) DEFAULT 0.00,
    approved smallint,
    mail_recipients character varying(255),
    continuous smallint,
    fictive_periodization smallint,
    contract_id character varying(30),
    tax_code integer,
    unspsc_code character varying(15),
    service_id integer,
    building_part character varying(4),
    order_dim1 integer,
    order_sent bigint,
    order_received bigint,
    order_received_amount numeric(20,2) DEFAULT 0.00,
    delivery_address text,
    file_attachments character varying(255)
);


ALTER TABLE public.fm_workorder OWNER TO portico;

--
-- Name: fm_workorder_status; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_workorder_status (
    id character varying(20) NOT NULL,
    descr character varying(255) NOT NULL,
    approved smallint,
    in_progress smallint,
    delivered smallint,
    closed smallint,
    canceled smallint
);


ALTER TABLE public.fm_workorder_status OWNER TO portico;

--
-- Name: fm_open_workorder_view; Type: VIEW; Schema: public; Owner: portico
--

CREATE VIEW public.fm_open_workorder_view AS
 SELECT fm_workorder.id,
    fm_workorder.project_id,
    fm_workorder_status.descr
   FROM (public.fm_workorder
     JOIN public.fm_workorder_status ON (((fm_workorder.status)::text = (fm_workorder_status.id)::text)))
  WHERE ((fm_workorder_status.delivered IS NULL) AND (fm_workorder_status.closed IS NULL));


ALTER TABLE public.fm_open_workorder_view OWNER TO portico;

--
-- Name: fm_order_dim1; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_order_dim1 (
    id integer NOT NULL,
    num character varying(20) NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_order_dim1 OWNER TO portico;

--
-- Name: seq_fm_order_template; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_order_template
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_order_template OWNER TO portico;

--
-- Name: fm_order_template; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_order_template (
    id integer DEFAULT nextval('public.seq_fm_order_template'::regclass) NOT NULL,
    name character varying(200) NOT NULL,
    content text,
    public_ smallint,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_order_template OWNER TO portico;

--
-- Name: fm_orders; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_orders (
    id integer DEFAULT 0 NOT NULL,
    type character varying(50) NOT NULL
);


ALTER TABLE public.fm_orders OWNER TO portico;

--
-- Name: fm_orders_actual_cost_view; Type: VIEW; Schema: public; Owner: portico
--

CREATE VIEW public.fm_orders_actual_cost_view AS
 SELECT fm_ecobilagoverf.pmwrkord_code AS order_id,
    sum(fm_ecobilagoverf.godkjentbelop) AS actual_cost
   FROM public.fm_ecobilagoverf
  GROUP BY fm_ecobilagoverf.pmwrkord_code;


ALTER TABLE public.fm_orders_actual_cost_view OWNER TO portico;

--
-- Name: fm_orders_paid_or_pending_view; Type: VIEW; Schema: public; Owner: portico
--

CREATE VIEW public.fm_orders_paid_or_pending_view AS
 SELECT orders_paid_or_pending.order_id,
    orders_paid_or_pending.periode,
    orders_paid_or_pending.amount,
    orders_paid_or_pending.periodization,
    orders_paid_or_pending.periodization_start,
    orders_paid_or_pending.mvakode
   FROM ( SELECT fm_ecobilagoverf.pmwrkord_code AS order_id,
            fm_ecobilagoverf.periode,
            sum(fm_ecobilagoverf.godkjentbelop) AS amount,
            fm_ecobilagoverf.periodization,
            fm_ecobilagoverf.periodization_start,
            fm_ecobilagoverf.mvakode
           FROM public.fm_ecobilagoverf
          GROUP BY fm_ecobilagoverf.pmwrkord_code, fm_ecobilagoverf.periode, fm_ecobilagoverf.periodization, fm_ecobilagoverf.periodization_start, fm_ecobilagoverf.mvakode
        UNION ALL
         SELECT fm_ecobilag.pmwrkord_code AS order_id,
            fm_ecobilag.periode,
            sum(fm_ecobilag.godkjentbelop) AS amount,
            fm_ecobilag.periodization,
            fm_ecobilag.periodization_start,
            fm_ecobilag.mvakode
           FROM public.fm_ecobilag
          GROUP BY fm_ecobilag.pmwrkord_code, fm_ecobilag.periode, fm_ecobilag.periodization, fm_ecobilag.periodization_start, fm_ecobilag.mvakode) orders_paid_or_pending
  ORDER BY orders_paid_or_pending.periode, orders_paid_or_pending.order_id;


ALTER TABLE public.fm_orders_paid_or_pending_view OWNER TO portico;

--
-- Name: fm_orders_pending_cost_view; Type: VIEW; Schema: public; Owner: portico
--

CREATE VIEW public.fm_orders_pending_cost_view AS
 SELECT fm_ecobilag.pmwrkord_code AS order_id,
    sum(fm_ecobilag.godkjentbelop) AS pending_cost
   FROM public.fm_ecobilag
  GROUP BY fm_ecobilag.pmwrkord_code;


ALTER TABLE public.fm_orders_pending_cost_view OWNER TO portico;

--
-- Name: fm_org_unit; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_org_unit (
    id integer NOT NULL,
    parent_id integer,
    name character varying(200) NOT NULL,
    active smallint DEFAULT 1,
    created_on integer NOT NULL,
    created_by integer NOT NULL,
    modified_by integer,
    modified_on integer
);


ALTER TABLE public.fm_org_unit OWNER TO portico;

--
-- Name: fm_owner; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_owner (
    id integer NOT NULL,
    abid integer,
    org_name character varying(50),
    contact_name character varying(50),
    category integer NOT NULL,
    member_of character varying(255),
    remark character varying(255),
    entry_date integer,
    owner_id integer
);


ALTER TABLE public.fm_owner OWNER TO portico;

--
-- Name: fm_owner_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_owner_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_owner_category OWNER TO portico;

--
-- Name: seq_fm_part_of_town; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_part_of_town
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_part_of_town OWNER TO portico;

--
-- Name: fm_part_of_town; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_part_of_town (
    id integer DEFAULT nextval('public.seq_fm_part_of_town'::regclass) NOT NULL,
    name character varying(150) NOT NULL,
    district_id smallint NOT NULL,
    delivery_address text
);


ALTER TABLE public.fm_part_of_town OWNER TO portico;

--
-- Name: fm_project; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_project (
    id integer NOT NULL,
    parent_id integer,
    project_type_id smallint,
    name character varying(255) NOT NULL,
    user_id integer NOT NULL,
    access character varying(7),
    category integer,
    entry_date integer NOT NULL,
    start_date bigint NOT NULL,
    end_date bigint,
    coordinator integer NOT NULL,
    status character varying(20) DEFAULT 'active'::character varying NOT NULL,
    descr text,
    budget numeric(20,2) DEFAULT 0.00,
    reserve numeric(20,2) DEFAULT 0.00,
    p_num character varying(15),
    p_entity_id integer,
    p_cat_id integer,
    location_code character varying(20),
    loc1 character varying(6),
    loc2 character varying(4),
    loc3 character varying(4),
    loc4 character varying(4),
    address character varying(150),
    tenant_id integer,
    contact_phone character varying(20),
    key_fetch integer,
    key_deliver integer,
    other_branch character varying(255),
    key_responsible integer,
    external_project_id integer,
    planned_cost integer DEFAULT 0,
    account_id character varying(20),
    ecodimb integer,
    contact_id integer,
    account_group integer,
    b_account_id character varying(20),
    inherit_location smallint DEFAULT 1,
    periodization_id integer,
    delivery_address text
);


ALTER TABLE public.fm_project OWNER TO portico;

--
-- Name: fm_project_budget; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_project_budget (
    project_id integer NOT NULL,
    year integer NOT NULL,
    month smallint DEFAULT 0 NOT NULL,
    budget numeric(20,2) DEFAULT 0.00,
    order_amount numeric(20,2) DEFAULT 0.00,
    closed smallint,
    active smallint,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_project_budget OWNER TO portico;

--
-- Name: fm_workorder_budget; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_workorder_budget (
    order_id bigint NOT NULL,
    year integer NOT NULL,
    month smallint DEFAULT 0 NOT NULL,
    budget numeric(20,2) DEFAULT 0.00,
    contract_sum numeric(20,2) DEFAULT 0.00,
    combined_cost numeric(20,2) DEFAULT 0.00,
    active smallint,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_workorder_budget OWNER TO portico;

--
-- Name: fm_project_budget_year_from_order_view; Type: VIEW; Schema: public; Owner: portico
--

CREATE VIEW public.fm_project_budget_year_from_order_view AS
 SELECT DISTINCT fm_workorder.project_id,
    fm_workorder_budget.year
   FROM (public.fm_workorder_budget
     JOIN public.fm_workorder ON ((fm_workorder.id = fm_workorder_budget.order_id)))
  ORDER BY fm_workorder.project_id;


ALTER TABLE public.fm_project_budget_year_from_order_view OWNER TO portico;

--
-- Name: fm_project_budget_year_view; Type: VIEW; Schema: public; Owner: portico
--

CREATE VIEW public.fm_project_budget_year_view AS
 SELECT DISTINCT fm_project_budget.project_id,
    fm_project_budget.year
   FROM public.fm_project_budget
  ORDER BY fm_project_budget.project_id;


ALTER TABLE public.fm_project_budget_year_view OWNER TO portico;

--
-- Name: seq_fm_project_buffer_budget; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_project_buffer_budget
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_project_buffer_budget OWNER TO portico;

--
-- Name: fm_project_buffer_budget; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_project_buffer_budget (
    id integer DEFAULT nextval('public.seq_fm_project_buffer_budget'::regclass) NOT NULL,
    year integer NOT NULL,
    month smallint DEFAULT 0 NOT NULL,
    buffer_project_id integer NOT NULL,
    entry_date integer NOT NULL,
    amount_in numeric(20,2) DEFAULT 0.00,
    from_project integer,
    amount_out numeric(20,2) DEFAULT 0.00,
    to_project integer,
    active smallint,
    user_id integer NOT NULL,
    remark text
);


ALTER TABLE public.fm_project_buffer_budget OWNER TO portico;

--
-- Name: seq_fm_project_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_project_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_project_history OWNER TO portico;

--
-- Name: fm_project_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_project_history (
    history_id integer DEFAULT nextval('public.seq_fm_project_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_appname character varying(64) NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.fm_project_history OWNER TO portico;

--
-- Name: fm_project_status; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_project_status (
    id character varying(20) NOT NULL,
    descr character varying(255) NOT NULL,
    approved smallint,
    closed smallint
);


ALTER TABLE public.fm_project_status OWNER TO portico;

--
-- Name: fm_projectbranch; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_projectbranch (
    project_id integer NOT NULL,
    branch_id integer NOT NULL
);


ALTER TABLE public.fm_projectbranch OWNER TO portico;

--
-- Name: fm_regulations; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_regulations (
    id integer NOT NULL,
    parent_id integer,
    name character varying(255) NOT NULL,
    descr text,
    external_ref character varying(255),
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_regulations OWNER TO portico;

--
-- Name: fm_request; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_request (
    id integer NOT NULL,
    condition_survey_id integer,
    title text,
    project_id integer,
    p_num character varying(15),
    p_entity_id integer,
    p_cat_id integer,
    location_code character varying(20),
    loc1 character varying(6),
    loc2 character varying(4),
    loc3 character varying(4),
    loc4 character varying(4),
    descr text,
    category integer,
    owner integer,
    access character varying(7),
    floor character varying(6),
    address character varying(150),
    tenant_id integer,
    contact_phone character varying(20),
    entry_date integer,
    amount_investment integer DEFAULT 0,
    amount_operation integer DEFAULT 0,
    amount_potential_grants integer DEFAULT 0,
    status character varying(10),
    branch_id integer,
    coordinator integer,
    responsible_unit integer,
    authorities_demands smallint DEFAULT 0,
    score integer DEFAULT 0,
    recommended_year integer DEFAULT 0,
    start_date bigint DEFAULT 0,
    end_date bigint DEFAULT 0,
    building_part character varying(4),
    closed_date integer,
    in_progress_date integer,
    delivered_date integer,
    regulations character varying(100),
    multiplier numeric(20,2) DEFAULT 1
);


ALTER TABLE public.fm_request OWNER TO portico;

--
-- Name: fm_request_condition; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_request_condition (
    request_id integer NOT NULL,
    condition_type integer NOT NULL,
    reference integer DEFAULT 0,
    degree integer DEFAULT 0,
    probability integer DEFAULT 0,
    consequence integer DEFAULT 0,
    user_id integer,
    entry_date integer
);


ALTER TABLE public.fm_request_condition OWNER TO portico;

--
-- Name: fm_request_condition_type; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_request_condition_type (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    descr character varying(50),
    priority_key integer DEFAULT 1
);


ALTER TABLE public.fm_request_condition_type OWNER TO portico;

--
-- Name: seq_fm_request_consume; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_request_consume
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_request_consume OWNER TO portico;

--
-- Name: fm_request_consume; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_request_consume (
    id integer DEFAULT nextval('public.seq_fm_request_consume'::regclass) NOT NULL,
    request_id integer NOT NULL,
    amount integer NOT NULL,
    date bigint NOT NULL,
    user_id integer,
    entry_date integer,
    descr text
);


ALTER TABLE public.fm_request_consume OWNER TO portico;

--
-- Name: seq_fm_request_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_request_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_request_history OWNER TO portico;

--
-- Name: fm_request_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_request_history (
    history_id integer DEFAULT nextval('public.seq_fm_request_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_appname character varying(64) NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.fm_request_history OWNER TO portico;

--
-- Name: seq_fm_request_planning; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_request_planning
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_request_planning OWNER TO portico;

--
-- Name: fm_request_planning; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_request_planning (
    id integer DEFAULT nextval('public.seq_fm_request_planning'::regclass) NOT NULL,
    request_id integer NOT NULL,
    amount integer NOT NULL,
    date bigint NOT NULL,
    user_id integer,
    entry_date integer,
    descr text
);


ALTER TABLE public.fm_request_planning OWNER TO portico;

--
-- Name: fm_request_responsible_unit; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_request_responsible_unit (
    id smallint NOT NULL,
    name character varying(50) NOT NULL,
    descr text
);


ALTER TABLE public.fm_request_responsible_unit OWNER TO portico;

--
-- Name: fm_request_status; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_request_status (
    id character varying(20) NOT NULL,
    descr character varying(255) NOT NULL,
    closed smallint,
    in_progress smallint,
    delivered smallint,
    sorting integer
);


ALTER TABLE public.fm_request_status OWNER TO portico;

--
-- Name: seq_fm_response_template; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_response_template
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_response_template OWNER TO portico;

--
-- Name: fm_response_template; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_response_template (
    id integer DEFAULT nextval('public.seq_fm_response_template'::regclass) NOT NULL,
    name character varying(200) NOT NULL,
    content text,
    public_ smallint,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_response_template OWNER TO portico;

--
-- Name: seq_fm_responsibility; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_responsibility
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_responsibility OWNER TO portico;

--
-- Name: fm_responsibility; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_responsibility (
    id integer DEFAULT nextval('public.seq_fm_responsibility'::regclass) NOT NULL,
    name character varying(50) NOT NULL,
    descr character varying(255),
    created_on integer NOT NULL,
    created_by integer NOT NULL
);


ALTER TABLE public.fm_responsibility OWNER TO portico;

--
-- Name: seq_fm_responsibility_contact; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_responsibility_contact
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_responsibility_contact OWNER TO portico;

--
-- Name: fm_responsibility_contact; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_responsibility_contact (
    id integer DEFAULT nextval('public.seq_fm_responsibility_contact'::regclass) NOT NULL,
    responsibility_role_id integer NOT NULL,
    contact_id integer,
    location_code character varying(20),
    p_num character varying(15),
    p_entity_id integer DEFAULT 0,
    p_cat_id integer DEFAULT 0,
    priority integer,
    active_from integer,
    active_to integer,
    created_on integer NOT NULL,
    created_by integer NOT NULL,
    expired_on integer,
    expired_by integer,
    remark text
);


ALTER TABLE public.fm_responsibility_contact OWNER TO portico;

--
-- Name: fm_responsibility_module; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_responsibility_module (
    responsibility_id integer NOT NULL,
    location_id integer NOT NULL,
    cat_id integer NOT NULL,
    active smallint,
    created_on integer NOT NULL,
    created_by integer NOT NULL
);


ALTER TABLE public.fm_responsibility_module OWNER TO portico;

--
-- Name: seq_fm_responsibility_role; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_responsibility_role
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_responsibility_role OWNER TO portico;

--
-- Name: fm_responsibility_role; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_responsibility_role (
    id integer DEFAULT nextval('public.seq_fm_responsibility_role'::regclass) NOT NULL,
    name character varying(200) NOT NULL,
    remark text,
    location_level character varying(200),
    responsibility_id integer,
    appname character varying(25) NOT NULL,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_responsibility_role OWNER TO portico;

--
-- Name: fm_s_agreement; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_s_agreement (
    id integer DEFAULT 0 NOT NULL,
    vendor_id integer,
    name character varying(100) NOT NULL,
    descr text,
    status character varying(10),
    category integer,
    member_of text,
    entry_date integer,
    start_date integer,
    end_date integer,
    termination_date integer,
    user_id integer,
    actual_cost numeric(20,2),
    account_id character varying(20)
);


ALTER TABLE public.fm_s_agreement OWNER TO portico;

--
-- Name: fm_s_agreement_budget; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_s_agreement_budget (
    agreement_id integer NOT NULL,
    year integer NOT NULL,
    budget_account character varying(15) NOT NULL,
    ecodimb integer,
    category integer,
    budget numeric(20,2) DEFAULT 0.00,
    actual_cost numeric(20,2) DEFAULT 0.00,
    user_id integer,
    entry_date integer,
    modified_date integer
);


ALTER TABLE public.fm_s_agreement_budget OWNER TO portico;

--
-- Name: fm_s_agreement_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_s_agreement_category (
    id integer DEFAULT 0 NOT NULL,
    descr character varying(50)
);


ALTER TABLE public.fm_s_agreement_category OWNER TO portico;

--
-- Name: fm_s_agreement_detail; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_s_agreement_detail (
    agreement_id integer DEFAULT 0 NOT NULL,
    id integer DEFAULT 0 NOT NULL,
    location_code character varying(30),
    address character varying(150),
    p_num character varying(15),
    p_entity_id integer DEFAULT 0,
    p_cat_id integer DEFAULT 0,
    descr text,
    unit integer,
    quantity numeric(20,2),
    frequency integer,
    user_id integer,
    entry_date integer,
    test text,
    cost numeric(20,2)
);


ALTER TABLE public.fm_s_agreement_detail OWNER TO portico;

--
-- Name: seq_fm_s_agreement_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_s_agreement_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_s_agreement_history OWNER TO portico;

--
-- Name: fm_s_agreement_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_s_agreement_history (
    history_id integer DEFAULT nextval('public.seq_fm_s_agreement_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_appname character varying(64) NOT NULL,
    history_detail_id integer NOT NULL,
    history_attrib_id integer NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.fm_s_agreement_history OWNER TO portico;

--
-- Name: fm_s_agreement_pricing; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_s_agreement_pricing (
    agreement_id integer DEFAULT 0 NOT NULL,
    item_id integer DEFAULT 0 NOT NULL,
    id integer DEFAULT 0 NOT NULL,
    current_index smallint,
    this_index numeric(20,4),
    cost numeric(20,2),
    index_date integer,
    user_id integer,
    entry_date integer
);


ALTER TABLE public.fm_s_agreement_pricing OWNER TO portico;

--
-- Name: fm_standard_unit; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_standard_unit (
    id integer NOT NULL,
    name character varying(20) NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_standard_unit OWNER TO portico;

--
-- Name: fm_streetaddress; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_streetaddress (
    id integer NOT NULL,
    descr character varying(150) NOT NULL
);


ALTER TABLE public.fm_streetaddress OWNER TO portico;

--
-- Name: seq_fm_template; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_template
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_template OWNER TO portico;

--
-- Name: fm_template; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_template (
    id integer DEFAULT nextval('public.seq_fm_template'::regclass) NOT NULL,
    name character varying(50),
    descr character varying(255),
    owner integer,
    chapter_id integer,
    entry_date integer
);


ALTER TABLE public.fm_template OWNER TO portico;

--
-- Name: seq_fm_template_hours; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_template_hours
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_template_hours OWNER TO portico;

--
-- Name: fm_template_hours; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_template_hours (
    id integer DEFAULT nextval('public.seq_fm_template_hours'::regclass) NOT NULL,
    template_id integer NOT NULL,
    record integer,
    owner integer NOT NULL,
    activity_id integer,
    activity_num character varying(15),
    grouping_id integer,
    grouping_descr character varying(50),
    hours_descr character varying(255),
    remark text,
    billperae numeric(20,2) DEFAULT 0.00,
    vendor_id integer,
    unit integer,
    ns3420_id character varying(20),
    tolerance integer,
    building_part character varying(4),
    quantity numeric(20,2),
    cost numeric(20,2),
    dim_d integer,
    entry_date integer
);


ALTER TABLE public.fm_template_hours OWNER TO portico;

--
-- Name: fm_tenant; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tenant (
    id integer NOT NULL,
    member_of character varying(255),
    entry_date integer,
    first_name character varying(30),
    last_name character varying(30),
    contact_phone character varying(20),
    contact_email character varying(64),
    category integer,
    phpgw_account_id integer,
    account_lid character varying(100),
    account_pwd character varying(115),
    account_status integer DEFAULT 1,
    owner_id integer
);


ALTER TABLE public.fm_tenant OWNER TO portico;

--
-- Name: fm_tenant_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tenant_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_tenant_category OWNER TO portico;

--
-- Name: seq_fm_tenant_claim; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_tenant_claim
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_tenant_claim OWNER TO portico;

--
-- Name: fm_tenant_claim; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tenant_claim (
    id integer DEFAULT nextval('public.seq_fm_tenant_claim'::regclass) NOT NULL,
    project_id integer NOT NULL,
    tenant_id integer NOT NULL,
    amount numeric(20,2) DEFAULT 0,
    b_account_id integer,
    category integer NOT NULL,
    status character varying(8),
    remark text,
    user_id integer NOT NULL,
    entry_date integer
);


ALTER TABLE public.fm_tenant_claim OWNER TO portico;

--
-- Name: fm_tenant_claim_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tenant_claim_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_tenant_claim_category OWNER TO portico;

--
-- Name: seq_fm_tenant_claim_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_tenant_claim_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_tenant_claim_history OWNER TO portico;

--
-- Name: fm_tenant_claim_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tenant_claim_history (
    history_id integer DEFAULT nextval('public.seq_fm_tenant_claim_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_appname character varying(64) NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.fm_tenant_claim_history OWNER TO portico;

--
-- Name: seq_fm_tts_budget; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_tts_budget
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_tts_budget OWNER TO portico;

--
-- Name: fm_tts_budget; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tts_budget (
    id integer DEFAULT nextval('public.seq_fm_tts_budget'::regclass) NOT NULL,
    ticket_id integer NOT NULL,
    amount numeric(20,2) DEFAULT 0 NOT NULL,
    period integer NOT NULL,
    remark text,
    created_on integer,
    created_by integer
);


ALTER TABLE public.fm_tts_budget OWNER TO portico;

--
-- Name: seq_fm_tts_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_tts_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_tts_history OWNER TO portico;

--
-- Name: fm_tts_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tts_history (
    history_id integer DEFAULT nextval('public.seq_fm_tts_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_appname character varying(64) NOT NULL,
    history_owner integer NOT NULL,
    history_status character varying(3) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL,
    publish smallint
);


ALTER TABLE public.fm_tts_history OWNER TO portico;

--
-- Name: seq_fm_tts_payments; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_tts_payments
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_tts_payments OWNER TO portico;

--
-- Name: fm_tts_payments; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tts_payments (
    id integer DEFAULT nextval('public.seq_fm_tts_payments'::regclass) NOT NULL,
    ticket_id integer NOT NULL,
    amount numeric(20,2) DEFAULT 0 NOT NULL,
    period integer NOT NULL,
    remark text,
    created_on integer,
    created_by integer
);


ALTER TABLE public.fm_tts_payments OWNER TO portico;

--
-- Name: fm_tts_priority; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tts_priority (
    id integer NOT NULL,
    name character varying(100)
);


ALTER TABLE public.fm_tts_priority OWNER TO portico;

--
-- Name: seq_fm_tts_status; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_tts_status
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_tts_status OWNER TO portico;

--
-- Name: fm_tts_status; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tts_status (
    id integer DEFAULT nextval('public.seq_fm_tts_status'::regclass) NOT NULL,
    name character varying(50) NOT NULL,
    color character varying(10),
    closed smallint,
    approved smallint,
    in_progress smallint,
    delivered smallint,
    actual_cost smallint,
    sorting integer
);


ALTER TABLE public.fm_tts_status OWNER TO portico;

--
-- Name: seq_fm_tts_tickets; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_tts_tickets
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_tts_tickets OWNER TO portico;

--
-- Name: fm_tts_tickets; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tts_tickets (
    id integer DEFAULT nextval('public.seq_fm_tts_tickets'::regclass) NOT NULL,
    group_id integer,
    priority smallint NOT NULL,
    user_id integer,
    assignedto integer,
    subject character varying(255),
    cat_id integer,
    billable_hours numeric(8,2),
    billable_rate numeric(8,2),
    status character varying(2) NOT NULL,
    details text NOT NULL,
    location_code character varying(50),
    p_num character varying(15),
    p_entity_id integer,
    p_cat_id integer,
    loc1 character varying(6),
    loc2 character varying(4),
    loc3 character varying(4),
    loc4 character varying(4),
    floor character varying(6),
    address character varying(255),
    contact_phone character varying(20),
    contact_email character varying(64),
    tenant_id integer,
    entry_date integer,
    finnish_date bigint,
    finnish_date2 bigint,
    contact_id integer,
    order_id bigint,
    ordered_by integer,
    vendor_id integer,
    contract_id character varying(30),
    tax_code integer,
    external_project_id character varying(10),
    unspsc_code character varying(15),
    service_id integer,
    order_descr text,
    b_account_id character varying(20),
    ecodimb integer,
    budget integer,
    actual_cost numeric(20,2) DEFAULT 0.00,
    actual_cost_year integer,
    order_cat_id integer,
    building_part character varying(4),
    order_dim1 integer,
    publish_note smallint,
    branch_id integer,
    modified_date integer,
    order_sent bigint,
    order_received bigint,
    order_received_amount numeric(20,2) DEFAULT 0.00,
    mail_recipients character varying(255),
    file_attachments character varying(255),
    delivery_address text,
    continuous smallint,
    order_deadline bigint,
    order_deadline2 bigint,
    invoice_remark text
);


ALTER TABLE public.fm_tts_tickets OWNER TO portico;

--
-- Name: fm_tts_views; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_tts_views (
    id integer NOT NULL,
    account_id integer,
    "time" integer NOT NULL
);


ALTER TABLE public.fm_tts_views OWNER TO portico;

--
-- Name: fm_unspsc_code; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_unspsc_code (
    id character varying(15) NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.fm_unspsc_code OWNER TO portico;

--
-- Name: fm_vendor; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_vendor (
    id integer NOT NULL,
    entry_date bigint DEFAULT date_part('epoch'::text, now()),
    org_name character varying(100),
    email character varying(64),
    contact_phone character varying(20),
    klasse character varying(10),
    member_of character varying(255),
    category smallint,
    mva integer,
    owner_id integer,
    active smallint DEFAULT 1
);


ALTER TABLE public.fm_vendor OWNER TO portico;

--
-- Name: fm_vendor_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_vendor_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_vendor_category OWNER TO portico;

--
-- Name: seq_fm_view_dataset; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_view_dataset
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_view_dataset OWNER TO portico;

--
-- Name: fm_view_dataset; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_view_dataset (
    id integer DEFAULT nextval('public.seq_fm_view_dataset'::regclass) NOT NULL,
    view_name character varying(100) NOT NULL,
    dataset_name character varying(100) NOT NULL,
    owner_id integer,
    entry_date integer
);


ALTER TABLE public.fm_view_dataset OWNER TO portico;

--
-- Name: seq_fm_view_dataset_report; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_view_dataset_report
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_view_dataset_report OWNER TO portico;

--
-- Name: fm_view_dataset_report; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_view_dataset_report (
    id integer DEFAULT nextval('public.seq_fm_view_dataset_report'::regclass) NOT NULL,
    dataset_id integer NOT NULL,
    report_name character varying(100) NOT NULL,
    report_definition jsonb,
    owner_id integer,
    entry_date integer
);


ALTER TABLE public.fm_view_dataset_report OWNER TO portico;

--
-- Name: fm_wo_h_deviation; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_wo_h_deviation (
    workorder_id bigint NOT NULL,
    hour_id integer NOT NULL,
    id integer NOT NULL,
    amount integer NOT NULL,
    descr text,
    entry_date integer
);


ALTER TABLE public.fm_wo_h_deviation OWNER TO portico;

--
-- Name: seq_fm_wo_hours; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_wo_hours
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_wo_hours OWNER TO portico;

--
-- Name: fm_wo_hours; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_wo_hours (
    id integer DEFAULT nextval('public.seq_fm_wo_hours'::regclass) NOT NULL,
    record integer,
    owner integer NOT NULL,
    workorder_id bigint NOT NULL,
    activity_id integer,
    activity_num character varying(15),
    grouping_id integer,
    grouping_descr character varying(50),
    entry_date integer NOT NULL,
    hours_descr text,
    remark text,
    billperae numeric(20,2) DEFAULT 0.00,
    vendor_id integer,
    unit integer,
    ns3420_id character varying(20),
    tolerance integer,
    building_part character varying(4),
    quantity numeric(20,2),
    cost numeric(20,2),
    dim_d integer,
    category integer,
    cat_per_cent integer
);


ALTER TABLE public.fm_wo_hours OWNER TO portico;

--
-- Name: fm_wo_hours_category; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_wo_hours_category (
    id integer NOT NULL,
    descr character varying(255) NOT NULL
);


ALTER TABLE public.fm_wo_hours_category OWNER TO portico;

--
-- Name: seq_fm_workorder_history; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_fm_workorder_history
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_fm_workorder_history OWNER TO portico;

--
-- Name: fm_workorder_history; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.fm_workorder_history (
    history_id integer DEFAULT nextval('public.seq_fm_workorder_history'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    history_appname character varying(64) NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_old_value text,
    history_timestamp timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.fm_workorder_history OWNER TO portico;

--
-- Name: phpgw_access_log; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_access_log (
    sessionid character(64) NOT NULL,
    loginid character varying(100) NOT NULL,
    ip character varying(100) DEFAULT '::1'::character varying NOT NULL,
    li integer NOT NULL,
    lo integer DEFAULT 0,
    account_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.phpgw_access_log OWNER TO portico;

--
-- Name: seq_phpgw_account_delegates; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_account_delegates
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_account_delegates OWNER TO portico;

--
-- Name: phpgw_account_delegates; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_account_delegates (
    delegate_id integer DEFAULT nextval('public.seq_phpgw_account_delegates'::regclass) NOT NULL,
    account_id integer NOT NULL,
    owner_id integer NOT NULL,
    location_id integer NOT NULL,
    data text,
    active_from integer,
    active_to integer,
    created_on integer NOT NULL,
    created_by integer NOT NULL
);


ALTER TABLE public.phpgw_account_delegates OWNER TO portico;

--
-- Name: seq_phpgw_accounts; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_accounts
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_accounts OWNER TO portico;

--
-- Name: phpgw_accounts; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_accounts (
    account_id integer DEFAULT nextval('public.seq_phpgw_accounts'::regclass) NOT NULL,
    account_lid character varying(100) NOT NULL,
    account_pwd character varying(115) NOT NULL,
    account_firstname character varying(50) NOT NULL,
    account_lastname character varying(50) NOT NULL,
    account_permissions text,
    account_groups character varying(30),
    account_lastlogin integer,
    account_lastloginfrom character varying(255),
    account_lastpwd_change integer,
    account_status character(1) DEFAULT 'A'::bpchar NOT NULL,
    account_expires integer NOT NULL,
    account_type character(1),
    person_id integer,
    account_quota integer DEFAULT '-1'::integer
);


ALTER TABLE public.phpgw_accounts OWNER TO portico;

--
-- Name: phpgw_accounts_data; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_accounts_data (
    account_id integer NOT NULL,
    account_data jsonb
);


ALTER TABLE public.phpgw_accounts_data OWNER TO portico;

--
-- Name: phpgw_acl; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_acl (
    acl_account integer,
    acl_rights integer,
    acl_grantor integer DEFAULT '-1'::integer,
    acl_type smallint DEFAULT 0,
    location_id integer,
    modified_on integer NOT NULL,
    modified_by integer DEFAULT '-1'::integer NOT NULL
);


ALTER TABLE public.phpgw_acl OWNER TO portico;

--
-- Name: seq_phpgw_applications; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_applications
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_applications OWNER TO portico;

--
-- Name: phpgw_applications; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_applications (
    app_id integer DEFAULT nextval('public.seq_phpgw_applications'::regclass) NOT NULL,
    app_name character varying(25) NOT NULL,
    app_enabled integer NOT NULL,
    app_order integer NOT NULL,
    app_tables text NOT NULL,
    app_version character varying(20) DEFAULT 0.0 NOT NULL
);


ALTER TABLE public.phpgw_applications OWNER TO portico;

--
-- Name: phpgw_async; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_async (
    id character varying(255) NOT NULL,
    next integer NOT NULL,
    times character varying(255) NOT NULL,
    method character varying(80) NOT NULL,
    data text NOT NULL,
    account_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.phpgw_async OWNER TO portico;

--
-- Name: phpgw_cache_user; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_cache_user (
    item_key character varying(100) NOT NULL,
    user_id integer NOT NULL,
    cache_data text NOT NULL,
    lastmodts integer NOT NULL
);


ALTER TABLE public.phpgw_cache_user OWNER TO portico;

--
-- Name: seq_phpgw_categories; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_categories
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_categories OWNER TO portico;

--
-- Name: phpgw_categories; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_categories (
    cat_id integer DEFAULT nextval('public.seq_phpgw_categories'::regclass) NOT NULL,
    cat_main integer DEFAULT 0 NOT NULL,
    cat_parent integer DEFAULT 0 NOT NULL,
    cat_level smallint DEFAULT 0 NOT NULL,
    cat_owner integer DEFAULT 0 NOT NULL,
    cat_access character varying(7),
    cat_appname character varying(50) NOT NULL,
    cat_name character varying(150) NOT NULL,
    cat_description character varying(255) NOT NULL,
    cat_data text,
    last_mod integer DEFAULT 0 NOT NULL,
    location_id integer DEFAULT 0,
    active smallint DEFAULT 1
);


ALTER TABLE public.phpgw_categories OWNER TO portico;

--
-- Name: phpgw_config; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_config (
    config_app character varying(50) NOT NULL,
    config_name character varying(255) NOT NULL,
    config_value text
);


ALTER TABLE public.phpgw_config OWNER TO portico;

--
-- Name: phpgw_config2_attrib; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_config2_attrib (
    section_id integer NOT NULL,
    id integer NOT NULL,
    input_type character varying(10) NOT NULL,
    name character varying(50) NOT NULL,
    descr character varying(200)
);


ALTER TABLE public.phpgw_config2_attrib OWNER TO portico;

--
-- Name: phpgw_config2_choice; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_config2_choice (
    section_id integer NOT NULL,
    attrib_id integer NOT NULL,
    id integer NOT NULL,
    value character varying(50) NOT NULL
);


ALTER TABLE public.phpgw_config2_choice OWNER TO portico;

--
-- Name: phpgw_config2_section; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_config2_section (
    id integer NOT NULL,
    location_id integer NOT NULL,
    name character varying(50) NOT NULL,
    descr character varying(200),
    data text
);


ALTER TABLE public.phpgw_config2_section OWNER TO portico;

--
-- Name: phpgw_config2_value; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_config2_value (
    section_id integer NOT NULL,
    attrib_id integer NOT NULL,
    id integer NOT NULL,
    value text NOT NULL
);


ALTER TABLE public.phpgw_config2_value OWNER TO portico;

--
-- Name: seq_phpgw_contact; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact OWNER TO portico;

--
-- Name: phpgw_contact; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact (
    contact_id integer DEFAULT nextval('public.seq_phpgw_contact'::regclass) NOT NULL,
    owner integer NOT NULL,
    access character varying(7),
    cat_id character varying(200),
    contact_type_id integer NOT NULL
);


ALTER TABLE public.phpgw_contact OWNER TO portico;

--
-- Name: seq_phpgw_contact_addr; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact_addr
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact_addr OWNER TO portico;

--
-- Name: phpgw_contact_addr; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_addr (
    contact_addr_id integer DEFAULT nextval('public.seq_phpgw_contact_addr'::regclass) NOT NULL,
    contact_id integer NOT NULL,
    addr_type_id integer,
    add1 character varying(64),
    add2 character varying(64),
    add3 character varying(64),
    city character varying(64),
    state character varying(64),
    postal_code character varying(64),
    country character varying(64),
    tz character varying(40),
    preferred character(1) DEFAULT 'N'::bpchar NOT NULL,
    created_on integer NOT NULL,
    created_by integer NOT NULL,
    modified_on integer NOT NULL,
    modified_by integer NOT NULL
);


ALTER TABLE public.phpgw_contact_addr OWNER TO portico;

--
-- Name: seq_phpgw_contact_addr_type; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact_addr_type
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact_addr_type OWNER TO portico;

--
-- Name: phpgw_contact_addr_type; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_addr_type (
    addr_type_id integer DEFAULT nextval('public.seq_phpgw_contact_addr_type'::regclass) NOT NULL,
    description character varying(50) NOT NULL
);


ALTER TABLE public.phpgw_contact_addr_type OWNER TO portico;

--
-- Name: seq_phpgw_contact_comm; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact_comm
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact_comm OWNER TO portico;

--
-- Name: phpgw_contact_comm; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_comm (
    comm_id integer DEFAULT nextval('public.seq_phpgw_contact_comm'::regclass) NOT NULL,
    contact_id integer NOT NULL,
    comm_descr_id integer NOT NULL,
    preferred character(1) DEFAULT 'N'::bpchar NOT NULL,
    comm_data character varying(255) NOT NULL,
    created_on integer NOT NULL,
    created_by integer NOT NULL,
    modified_on integer NOT NULL,
    modified_by integer NOT NULL
);


ALTER TABLE public.phpgw_contact_comm OWNER TO portico;

--
-- Name: seq_phpgw_contact_comm_descr; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact_comm_descr
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact_comm_descr OWNER TO portico;

--
-- Name: phpgw_contact_comm_descr; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_comm_descr (
    comm_descr_id integer DEFAULT nextval('public.seq_phpgw_contact_comm_descr'::regclass) NOT NULL,
    comm_type_id integer NOT NULL,
    descr character varying(50)
);


ALTER TABLE public.phpgw_contact_comm_descr OWNER TO portico;

--
-- Name: seq_phpgw_contact_comm_type; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact_comm_type
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact_comm_type OWNER TO portico;

--
-- Name: phpgw_contact_comm_type; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_comm_type (
    comm_type_id integer DEFAULT nextval('public.seq_phpgw_contact_comm_type'::regclass) NOT NULL,
    type character varying(50),
    active character varying(30),
    class character varying(30)
);


ALTER TABLE public.phpgw_contact_comm_type OWNER TO portico;

--
-- Name: seq_phpgw_contact_note; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact_note
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact_note OWNER TO portico;

--
-- Name: phpgw_contact_note; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_note (
    contact_note_id integer DEFAULT nextval('public.seq_phpgw_contact_note'::regclass) NOT NULL,
    contact_id integer NOT NULL,
    note_type_id integer NOT NULL,
    note_text text NOT NULL,
    created_on integer NOT NULL,
    created_by integer NOT NULL,
    modified_on integer NOT NULL,
    modified_by integer NOT NULL
);


ALTER TABLE public.phpgw_contact_note OWNER TO portico;

--
-- Name: seq_phpgw_contact_note_type; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact_note_type
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact_note_type OWNER TO portico;

--
-- Name: phpgw_contact_note_type; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_note_type (
    note_type_id integer DEFAULT nextval('public.seq_phpgw_contact_note_type'::regclass) NOT NULL,
    description character varying(30) NOT NULL
);


ALTER TABLE public.phpgw_contact_note_type OWNER TO portico;

--
-- Name: phpgw_contact_org; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_org (
    org_id integer NOT NULL,
    name character varying(80) NOT NULL,
    active character(1) DEFAULT 'Y'::bpchar,
    parent integer,
    created_on integer NOT NULL,
    created_by integer NOT NULL,
    modified_on integer NOT NULL,
    modified_by integer NOT NULL
);


ALTER TABLE public.phpgw_contact_org OWNER TO portico;

--
-- Name: phpgw_contact_org_person; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_org_person (
    org_id integer NOT NULL,
    person_id integer NOT NULL,
    addr_id integer,
    preferred character(1) DEFAULT 'N'::bpchar NOT NULL,
    created_on integer NOT NULL,
    created_by integer NOT NULL
);


ALTER TABLE public.phpgw_contact_org_person OWNER TO portico;

--
-- Name: seq_phpgw_contact_others; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact_others
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact_others OWNER TO portico;

--
-- Name: phpgw_contact_others; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_others (
    other_id integer DEFAULT nextval('public.seq_phpgw_contact_others'::regclass) NOT NULL,
    contact_id integer NOT NULL,
    contact_owner integer NOT NULL,
    other_name character varying(255) NOT NULL,
    other_value text NOT NULL
);


ALTER TABLE public.phpgw_contact_others OWNER TO portico;

--
-- Name: phpgw_contact_person; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_person (
    person_id integer NOT NULL,
    first_name character varying(64) NOT NULL,
    last_name character varying(64) NOT NULL,
    middle_name character varying(64),
    prefix character varying(64),
    suffix character varying(64),
    birthday character varying(32),
    pubkey text,
    title character varying(64),
    department character varying(64),
    initials character varying(10),
    sound character varying(64),
    active character(1) DEFAULT 'Y'::bpchar,
    created_on integer NOT NULL,
    created_by integer NOT NULL,
    modified_on integer NOT NULL,
    modified_by integer NOT NULL
);


ALTER TABLE public.phpgw_contact_person OWNER TO portico;

--
-- Name: seq_phpgw_contact_types; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_contact_types
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_contact_types OWNER TO portico;

--
-- Name: phpgw_contact_types; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_contact_types (
    contact_type_id integer DEFAULT nextval('public.seq_phpgw_contact_types'::regclass) NOT NULL,
    contact_type_descr character varying(50),
    contact_type_table character varying(50)
);


ALTER TABLE public.phpgw_contact_types OWNER TO portico;

--
-- Name: phpgw_cust_attribute; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_cust_attribute (
    location_id integer NOT NULL,
    group_id integer DEFAULT 0,
    id integer NOT NULL,
    column_name character varying(50) NOT NULL,
    input_text character varying(255) NOT NULL,
    statustext character varying(255) NOT NULL,
    datatype character varying(10) NOT NULL,
    search smallint,
    history smallint,
    list integer,
    attrib_sort integer,
    size integer,
    precision_ integer,
    scale integer,
    default_value character varying(20),
    nullable character varying(5),
    disabled smallint,
    lookup_form smallint,
    custom smallint DEFAULT 1,
    helpmsg text,
    get_list_function character varying(255),
    get_list_function_input character varying(255),
    get_single_function character varying(255),
    get_single_function_input character varying(255),
    short_description smallint,
    javascript_action text
);


ALTER TABLE public.phpgw_cust_attribute OWNER TO portico;

--
-- Name: phpgw_cust_attribute_group; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_cust_attribute_group (
    location_id integer NOT NULL,
    id integer NOT NULL,
    parent_id integer,
    name character varying(100) NOT NULL,
    group_sort smallint NOT NULL,
    descr character varying(150),
    remark text
);


ALTER TABLE public.phpgw_cust_attribute_group OWNER TO portico;

--
-- Name: phpgw_cust_choice; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_cust_choice (
    location_id integer NOT NULL,
    attrib_id integer NOT NULL,
    id integer NOT NULL,
    value text NOT NULL,
    title text,
    choice_sort integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.phpgw_cust_choice OWNER TO portico;

--
-- Name: phpgw_cust_function; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_cust_function (
    location_id integer NOT NULL,
    id integer NOT NULL,
    descr text,
    file_name character varying(255) NOT NULL,
    active smallint,
    pre_commit smallint,
    client_side smallint,
    custom_sort integer
);


ALTER TABLE public.phpgw_cust_function OWNER TO portico;

--
-- Name: phpgw_group_map; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_group_map (
    group_id integer NOT NULL,
    account_id integer NOT NULL,
    arights integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.phpgw_group_map OWNER TO portico;

--
-- Name: seq_phpgw_history_log; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_history_log
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_history_log OWNER TO portico;

--
-- Name: phpgw_history_log; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_history_log (
    history_id integer DEFAULT nextval('public.seq_phpgw_history_log'::regclass) NOT NULL,
    history_record_id integer NOT NULL,
    app_id character varying(64) NOT NULL,
    history_owner integer NOT NULL,
    history_status character(2) NOT NULL,
    history_new_value text NOT NULL,
    history_timestamp timestamp without time zone NOT NULL,
    history_old_value text,
    location_id integer
);


ALTER TABLE public.phpgw_history_log OWNER TO portico;

--
-- Name: seq_phpgw_hooks; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_hooks
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_hooks OWNER TO portico;

--
-- Name: phpgw_hooks; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_hooks (
    hook_id integer DEFAULT nextval('public.seq_phpgw_hooks'::regclass) NOT NULL,
    hook_appname character varying(255),
    hook_location character varying(255),
    hook_filename character varying(255)
);


ALTER TABLE public.phpgw_hooks OWNER TO portico;

--
-- Name: seq_phpgw_interlink; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_interlink
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_interlink OWNER TO portico;

--
-- Name: phpgw_interlink; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_interlink (
    interlink_id integer DEFAULT nextval('public.seq_phpgw_interlink'::regclass) NOT NULL,
    location1_id integer NOT NULL,
    location1_item_id integer NOT NULL,
    location2_id integer NOT NULL,
    location2_item_id integer NOT NULL,
    is_private smallint NOT NULL,
    account_id integer NOT NULL,
    entry_date integer NOT NULL,
    start_date integer NOT NULL,
    end_date integer NOT NULL
);


ALTER TABLE public.phpgw_interlink OWNER TO portico;

--
-- Name: seq_phpgw_interserv; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_interserv
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_interserv OWNER TO portico;

--
-- Name: phpgw_interserv; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_interserv (
    server_id integer DEFAULT nextval('public.seq_phpgw_interserv'::regclass) NOT NULL,
    server_name character varying(64),
    server_host character varying(255),
    server_url character varying(255),
    trust_level integer,
    trust_rel integer,
    username character varying(64),
    password character varying(255),
    admin_name character varying(255),
    admin_email character varying(255),
    server_mode character varying(16) DEFAULT 'xmlrpc'::character varying NOT NULL,
    server_security character varying(16)
);


ALTER TABLE public.phpgw_interserv OWNER TO portico;

--
-- Name: phpgw_lang; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_lang (
    message_id character varying(255) NOT NULL,
    app_name character varying(30) DEFAULT 'common'::character varying NOT NULL,
    lang character varying(5) NOT NULL,
    content text
);


ALTER TABLE public.phpgw_lang OWNER TO portico;

--
-- Name: phpgw_languages; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_languages (
    lang_id character varying(2) NOT NULL,
    lang_name character varying(50) NOT NULL,
    available character(3) DEFAULT 'No'::bpchar NOT NULL
);


ALTER TABLE public.phpgw_languages OWNER TO portico;

--
-- Name: seq_phpgw_locations; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_locations
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_locations OWNER TO portico;

--
-- Name: phpgw_locations; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_locations (
    location_id integer DEFAULT nextval('public.seq_phpgw_locations'::regclass) NOT NULL,
    app_id integer NOT NULL,
    name character varying(50) NOT NULL,
    descr character varying(100) NOT NULL,
    allow_grant smallint,
    allow_c_attrib smallint,
    c_attrib_table character varying(25),
    allow_c_function smallint
);


ALTER TABLE public.phpgw_locations OWNER TO portico;

--
-- Name: seq_phpgw_log; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_log
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_log OWNER TO portico;

--
-- Name: phpgw_log; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_log (
    log_id integer DEFAULT nextval('public.seq_phpgw_log'::regclass) NOT NULL,
    log_date timestamp without time zone NOT NULL,
    log_account_id integer NOT NULL,
    log_account_lid character varying(100) NOT NULL,
    log_app character varying(25) NOT NULL,
    log_severity character(2) NOT NULL,
    log_file character varying(255) NOT NULL,
    log_line integer DEFAULT 0 NOT NULL,
    log_msg text NOT NULL
);


ALTER TABLE public.phpgw_log OWNER TO portico;

--
-- Name: seq_phpgw_mail_handler; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_mail_handler
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_mail_handler OWNER TO portico;

--
-- Name: phpgw_mail_handler; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_mail_handler (
    handler_id integer DEFAULT nextval('public.seq_phpgw_mail_handler'::regclass) NOT NULL,
    target_email character varying(75) NOT NULL,
    handler character varying(50) NOT NULL,
    is_active integer NOT NULL,
    lastmod bigint NOT NULL,
    lastmod_user bigint NOT NULL
);


ALTER TABLE public.phpgw_mail_handler OWNER TO portico;

--
-- Name: phpgw_mapping; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_mapping (
    ext_user character varying(100) NOT NULL,
    auth_type character varying(25) NOT NULL,
    status character(1) DEFAULT 'A'::bpchar NOT NULL,
    location character varying(200) NOT NULL,
    account_lid character varying(100) NOT NULL
);


ALTER TABLE public.phpgw_mapping OWNER TO portico;

--
-- Name: phpgw_nextid; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_nextid (
    id integer,
    appname character varying(25) NOT NULL
);


ALTER TABLE public.phpgw_nextid OWNER TO portico;

--
-- Name: seq_phpgw_notification; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_notification
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_notification OWNER TO portico;

--
-- Name: phpgw_notification; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_notification (
    id integer DEFAULT nextval('public.seq_phpgw_notification'::regclass) NOT NULL,
    location_id integer NOT NULL,
    location_item_id bigint NOT NULL,
    contact_id integer NOT NULL,
    is_active smallint,
    notification_method character varying(20),
    user_id integer NOT NULL,
    entry_date integer NOT NULL
);


ALTER TABLE public.phpgw_notification OWNER TO portico;

--
-- Name: phpgw_preferences; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_preferences (
    preference_owner integer NOT NULL,
    preference_app character varying(25) NOT NULL,
    preference_value text NOT NULL
);


ALTER TABLE public.phpgw_preferences OWNER TO portico;

--
-- Name: phpgw_sessions; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_sessions (
    session_id character varying(255) NOT NULL,
    ip character varying(100),
    data text,
    lastmodts integer
);


ALTER TABLE public.phpgw_sessions OWNER TO portico;

--
-- Name: seq_phpgw_vfs; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_vfs
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_vfs OWNER TO portico;

--
-- Name: phpgw_vfs; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_vfs (
    file_id integer DEFAULT nextval('public.seq_phpgw_vfs'::regclass) NOT NULL,
    owner_id integer NOT NULL,
    createdby_id integer,
    modifiedby_id integer,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone,
    size integer,
    mime_type character varying(150),
    deleteable character(1) DEFAULT 'Y'::bpchar,
    comment text,
    app character varying(25),
    directory text,
    name text NOT NULL,
    link_directory text,
    link_name text,
    version character varying(30) DEFAULT '0.0.0.0'::character varying NOT NULL,
    content text,
    external_id bigint,
    md5_sum character varying(64)
);


ALTER TABLE public.phpgw_vfs OWNER TO portico;

--
-- Name: seq_phpgw_vfs_file_relation; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_phpgw_vfs_file_relation
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_phpgw_vfs_file_relation OWNER TO portico;

--
-- Name: phpgw_vfs_file_relation; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_vfs_file_relation (
    relation_id integer DEFAULT nextval('public.seq_phpgw_vfs_file_relation'::regclass) NOT NULL,
    file_id integer NOT NULL,
    location_id integer NOT NULL,
    location_item_id integer NOT NULL,
    is_private smallint NOT NULL,
    account_id integer NOT NULL,
    entry_date bigint NOT NULL,
    start_date bigint NOT NULL,
    end_date bigint NOT NULL
);


ALTER TABLE public.phpgw_vfs_file_relation OWNER TO portico;

--
-- Name: phpgw_vfs_filedata; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.phpgw_vfs_filedata (
    file_id integer NOT NULL,
    metadata jsonb NOT NULL
);


ALTER TABLE public.phpgw_vfs_filedata OWNER TO portico;

--
-- Name: seq_rental_adjustment; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_adjustment
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_adjustment OWNER TO portico;

--
-- Name: rental_adjustment; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_adjustment (
    id integer DEFAULT nextval('public.seq_rental_adjustment'::regclass) NOT NULL,
    price_item_id integer,
    responsibility_id integer NOT NULL,
    adjustment_date bigint,
    adjustment_type character varying(255),
    new_price numeric(20,2),
    percent_ numeric(20,2),
    adjustment_interval integer,
    is_manual boolean DEFAULT false NOT NULL,
    extra_adjustment boolean DEFAULT false NOT NULL,
    is_executed boolean DEFAULT false NOT NULL
);


ALTER TABLE public.rental_adjustment OWNER TO portico;

--
-- Name: seq_rental_application; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_application
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_application OWNER TO portico;

--
-- Name: rental_application; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_application (
    id integer DEFAULT nextval('public.seq_rental_application'::regclass) NOT NULL,
    ecodimb_id integer NOT NULL,
    district_id integer NOT NULL,
    composite_type_id integer NOT NULL,
    cleaning smallint,
    payment_method smallint NOT NULL,
    date_start bigint,
    date_end bigint,
    assign_date_start bigint,
    assign_date_end bigint,
    entry_date bigint,
    identifier character varying(20) NOT NULL,
    adjustment_type character varying(255),
    firstname character varying(64),
    lastname character varying(64),
    job_title character varying(255),
    company_name character varying(255),
    department character varying(255),
    address1 character varying(255),
    address2 character varying(255),
    postal_code character varying(255),
    place character varying(255),
    phone character varying(255),
    email character varying(255),
    account_number character varying(255),
    unit_leader character varying(255),
    status smallint NOT NULL,
    executive_officer integer
);


ALTER TABLE public.rental_application OWNER TO portico;

--
-- Name: seq_rental_application_comment; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_application_comment
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_application_comment OWNER TO portico;

--
-- Name: rental_application_comment; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_application_comment (
    id integer DEFAULT nextval('public.seq_rental_application_comment'::regclass) NOT NULL,
    application_id integer NOT NULL,
    "time" bigint NOT NULL,
    author text NOT NULL,
    comment text NOT NULL,
    type character varying(20) DEFAULT 'comment'::character varying NOT NULL
);


ALTER TABLE public.rental_application_comment OWNER TO portico;

--
-- Name: seq_rental_application_composite; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_application_composite
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_application_composite OWNER TO portico;

--
-- Name: rental_application_composite; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_application_composite (
    id integer DEFAULT nextval('public.seq_rental_application_composite'::regclass) NOT NULL,
    application_id integer NOT NULL,
    composite_id integer NOT NULL
);


ALTER TABLE public.rental_application_composite OWNER TO portico;

--
-- Name: seq_rental_billing; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_billing
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_billing OWNER TO portico;

--
-- Name: rental_billing; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_billing (
    id integer DEFAULT nextval('public.seq_rental_billing'::regclass) NOT NULL,
    total_sum numeric(20,2),
    success boolean DEFAULT false NOT NULL,
    created_by integer,
    timestamp_start bigint,
    timestamp_stop bigint,
    timestamp_commit bigint,
    location_id integer NOT NULL,
    title character varying(255) NOT NULL,
    deleted boolean DEFAULT false,
    export_format character varying(255),
    export_data text,
    serial_start bigint,
    serial_end bigint
);


ALTER TABLE public.rental_billing OWNER TO portico;

--
-- Name: seq_rental_billing_info; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_billing_info
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_billing_info OWNER TO portico;

--
-- Name: rental_billing_info; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_billing_info (
    id integer DEFAULT nextval('public.seq_rental_billing_info'::regclass) NOT NULL,
    billing_id integer NOT NULL,
    location_id integer NOT NULL,
    term_id integer NOT NULL,
    year integer NOT NULL,
    month integer NOT NULL,
    deleted boolean DEFAULT false
);


ALTER TABLE public.rental_billing_info OWNER TO portico;

--
-- Name: seq_rental_billing_term; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_billing_term
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_billing_term OWNER TO portico;

--
-- Name: rental_billing_term; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_billing_term (
    id integer DEFAULT nextval('public.seq_rental_billing_term'::regclass) NOT NULL,
    title character varying(255) NOT NULL,
    months integer NOT NULL
);


ALTER TABLE public.rental_billing_term OWNER TO portico;

--
-- Name: seq_rental_composite; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_composite
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_composite OWNER TO portico;

--
-- Name: rental_composite; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_composite (
    id integer DEFAULT nextval('public.seq_rental_composite'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    is_active boolean DEFAULT true NOT NULL,
    status_id smallint DEFAULT 1 NOT NULL,
    address_1 character varying(255),
    address_2 character varying(255),
    house_number character varying(255),
    postcode character varying(255),
    place character varying(255),
    has_custom_address boolean DEFAULT false NOT NULL,
    object_type_id smallint,
    composite_type_id smallint DEFAULT 1,
    area numeric(20,2),
    furnish_type_id integer,
    standard_id integer,
    part_of_town_id integer,
    custom_price_factor numeric(20,2) DEFAULT 1.00,
    custom_price numeric(20,2) DEFAULT 1.00,
    price_type_id smallint DEFAULT 1
);


ALTER TABLE public.rental_composite OWNER TO portico;

--
-- Name: rental_composite_standard; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_composite_standard (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    factor numeric(20,2)
);


ALTER TABLE public.rental_composite_standard OWNER TO portico;

--
-- Name: rental_composite_type; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_composite_type (
    id integer NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.rental_composite_type OWNER TO portico;

--
-- Name: rental_contract; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_contract (
    id integer NOT NULL,
    date_start bigint,
    date_end bigint,
    billing_start bigint,
    billing_end bigint,
    location_id integer NOT NULL,
    term_id integer,
    security_type integer,
    security_amount character varying(255),
    old_contract_id character varying(255),
    executive_officer integer,
    created bigint,
    created_by integer,
    comment text,
    last_updated bigint,
    service_id character varying(255),
    responsibility_id character varying(255),
    reference character varying(255),
    customer_order_id integer,
    invoice_header character varying(255),
    account_in character varying(255),
    account_out character varying(255),
    project_id character varying(255),
    due_date bigint,
    contract_type_id integer,
    rented_area numeric(20,2),
    adjustment_interval integer,
    adjustment_share integer DEFAULT 100,
    adjustment_year integer,
    adjustable boolean DEFAULT false,
    override_adjustment_start integer,
    publish_comment boolean DEFAULT false,
    notify_on_expire smallint,
    notified_time bigint
);


ALTER TABLE public.rental_contract OWNER TO portico;

--
-- Name: seq_rental_contract_composite; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_contract_composite
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_contract_composite OWNER TO portico;

--
-- Name: rental_contract_composite; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_contract_composite (
    id integer DEFAULT nextval('public.seq_rental_contract_composite'::regclass) NOT NULL,
    contract_id integer NOT NULL,
    composite_id integer NOT NULL
);


ALTER TABLE public.rental_contract_composite OWNER TO portico;

--
-- Name: rental_contract_last_edited; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_contract_last_edited (
    contract_id integer NOT NULL,
    account_id integer NOT NULL,
    edited_on bigint NOT NULL
);


ALTER TABLE public.rental_contract_last_edited OWNER TO portico;

--
-- Name: rental_contract_party; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_contract_party (
    contract_id integer NOT NULL,
    party_id integer NOT NULL,
    is_payer boolean DEFAULT false NOT NULL
);


ALTER TABLE public.rental_contract_party OWNER TO portico;

--
-- Name: seq_rental_contract_price_item; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_contract_price_item
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_contract_price_item OWNER TO portico;

--
-- Name: rental_contract_price_item; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_contract_price_item (
    id integer DEFAULT nextval('public.seq_rental_contract_price_item'::regclass) NOT NULL,
    price_item_id integer NOT NULL,
    contract_id integer NOT NULL,
    title character varying(255) NOT NULL,
    area numeric(20,2),
    count integer,
    agresso_id character varying(255),
    is_area boolean DEFAULT true NOT NULL,
    price numeric(20,2),
    total_price numeric(20,2),
    date_start bigint,
    date_end bigint,
    is_billed boolean DEFAULT false NOT NULL,
    is_one_time boolean DEFAULT false,
    billing_id integer
);


ALTER TABLE public.rental_contract_price_item OWNER TO portico;

--
-- Name: seq_rental_contract_responsibility; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_contract_responsibility
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_contract_responsibility OWNER TO portico;

--
-- Name: rental_contract_responsibility; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_contract_responsibility (
    id integer DEFAULT nextval('public.seq_rental_contract_responsibility'::regclass) NOT NULL,
    location_id integer NOT NULL,
    title character varying(255) NOT NULL,
    notify_before integer NOT NULL,
    notify_before_due_date integer NOT NULL,
    notify_after_termination_date integer NOT NULL,
    account_in character varying(255),
    account_out character varying(255),
    project_number character varying(255),
    agresso_export_format character varying(255)
);


ALTER TABLE public.rental_contract_responsibility OWNER TO portico;

--
-- Name: rental_contract_responsibility_unit; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_contract_responsibility_unit (
    id integer NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.rental_contract_responsibility_unit OWNER TO portico;

--
-- Name: seq_rental_contract_types; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_contract_types
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_contract_types OWNER TO portico;

--
-- Name: rental_contract_types; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_contract_types (
    id integer DEFAULT nextval('public.seq_rental_contract_types'::regclass) NOT NULL,
    label character varying(255) NOT NULL,
    responsibility_id integer NOT NULL,
    account character varying(255)
);


ALTER TABLE public.rental_contract_types OWNER TO portico;

--
-- Name: seq_rental_document; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_document
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_document OWNER TO portico;

--
-- Name: rental_document; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_document (
    id integer DEFAULT nextval('public.seq_rental_document'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    contract_id integer,
    party_id integer,
    title character varying(255),
    description text,
    type_id integer NOT NULL
);


ALTER TABLE public.rental_document OWNER TO portico;

--
-- Name: seq_rental_document_types; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_document_types
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_document_types OWNER TO portico;

--
-- Name: rental_document_types; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_document_types (
    id integer DEFAULT nextval('public.seq_rental_document_types'::regclass) NOT NULL,
    title character varying(255) NOT NULL
);


ALTER TABLE public.rental_document_types OWNER TO portico;

--
-- Name: seq_rental_email_out; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_email_out
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_email_out OWNER TO portico;

--
-- Name: rental_email_out; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_email_out (
    id integer DEFAULT nextval('public.seq_rental_email_out'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    remark text,
    subject text NOT NULL,
    content text,
    user_id integer,
    created bigint DEFAULT date_part('epoch'::text, now()),
    modified bigint DEFAULT date_part('epoch'::text, now())
);


ALTER TABLE public.rental_email_out OWNER TO portico;

--
-- Name: seq_rental_email_out_party; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_email_out_party
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_email_out_party OWNER TO portico;

--
-- Name: rental_email_out_party; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_email_out_party (
    id integer DEFAULT nextval('public.seq_rental_email_out_party'::regclass) NOT NULL,
    email_out_id integer,
    party_id integer,
    status smallint DEFAULT 0
);


ALTER TABLE public.rental_email_out_party OWNER TO portico;

--
-- Name: seq_rental_email_template; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_email_template
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_email_template OWNER TO portico;

--
-- Name: rental_email_template; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_email_template (
    id integer DEFAULT nextval('public.seq_rental_email_template'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    content text,
    public_ smallint,
    user_id integer,
    entry_date bigint DEFAULT date_part('epoch'::text, now()),
    modified_date bigint DEFAULT date_part('epoch'::text, now())
);


ALTER TABLE public.rental_email_template OWNER TO portico;

--
-- Name: seq_rental_invoice; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_invoice
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_invoice OWNER TO portico;

--
-- Name: rental_invoice; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_invoice (
    id integer DEFAULT nextval('public.seq_rental_invoice'::regclass) NOT NULL,
    contract_id integer NOT NULL,
    billing_id integer NOT NULL,
    party_id integer NOT NULL,
    timestamp_created bigint NOT NULL,
    timestamp_start bigint NOT NULL,
    timestamp_end bigint NOT NULL,
    total_sum numeric(20,2),
    total_area numeric(20,2),
    header character varying(255),
    account_in character varying(255),
    account_out character varying(255),
    service_id character varying(255),
    responsibility_id character varying(255),
    project_id character varying(255),
    serial_number bigint
);


ALTER TABLE public.rental_invoice OWNER TO portico;

--
-- Name: seq_rental_invoice_price_item; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_invoice_price_item
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_invoice_price_item OWNER TO portico;

--
-- Name: rental_invoice_price_item; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_invoice_price_item (
    id integer DEFAULT nextval('public.seq_rental_invoice_price_item'::regclass) NOT NULL,
    invoice_id integer NOT NULL,
    title character varying(255) NOT NULL,
    area numeric(20,2),
    count integer,
    agresso_id character varying(255),
    is_area boolean DEFAULT true NOT NULL,
    is_one_time boolean DEFAULT true NOT NULL,
    price numeric(20,2),
    total_price numeric(20,2),
    date_start date,
    date_end date
);


ALTER TABLE public.rental_invoice_price_item OWNER TO portico;

--
-- Name: seq_rental_location_factor; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_location_factor
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_location_factor OWNER TO portico;

--
-- Name: rental_location_factor; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_location_factor (
    id integer DEFAULT nextval('public.seq_rental_location_factor'::regclass) NOT NULL,
    part_of_town_id integer NOT NULL,
    factor numeric(20,2) DEFAULT 1.00 NOT NULL,
    remark text,
    user_id integer,
    entry_date bigint,
    modified_date bigint
);


ALTER TABLE public.rental_location_factor OWNER TO portico;

--
-- Name: seq_rental_movein; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_movein
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_movein OWNER TO portico;

--
-- Name: rental_movein; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_movein (
    id integer DEFAULT nextval('public.seq_rental_movein'::regclass) NOT NULL,
    contract_id integer NOT NULL,
    account_id integer NOT NULL,
    created bigint DEFAULT date_part('epoch'::text, now()) NOT NULL,
    modified bigint DEFAULT date_part('epoch'::text, now()) NOT NULL
);


ALTER TABLE public.rental_movein OWNER TO portico;

--
-- Name: seq_rental_movein_comment; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_movein_comment
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_movein_comment OWNER TO portico;

--
-- Name: rental_movein_comment; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_movein_comment (
    id integer DEFAULT nextval('public.seq_rental_movein_comment'::regclass) NOT NULL,
    movein_id integer NOT NULL,
    "time" bigint DEFAULT date_part('epoch'::text, now()) NOT NULL,
    author text NOT NULL,
    comment text NOT NULL,
    type character varying(20) DEFAULT 'comment'::character varying NOT NULL
);


ALTER TABLE public.rental_movein_comment OWNER TO portico;

--
-- Name: seq_rental_moveout; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_moveout
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_moveout OWNER TO portico;

--
-- Name: rental_moveout; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_moveout (
    id integer DEFAULT nextval('public.seq_rental_moveout'::regclass) NOT NULL,
    contract_id integer NOT NULL,
    account_id integer NOT NULL,
    created bigint DEFAULT date_part('epoch'::text, now()) NOT NULL,
    modified bigint DEFAULT date_part('epoch'::text, now()) NOT NULL
);


ALTER TABLE public.rental_moveout OWNER TO portico;

--
-- Name: seq_rental_moveout_comment; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_moveout_comment
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_moveout_comment OWNER TO portico;

--
-- Name: rental_moveout_comment; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_moveout_comment (
    id integer DEFAULT nextval('public.seq_rental_moveout_comment'::regclass) NOT NULL,
    moveout_id integer NOT NULL,
    "time" bigint DEFAULT date_part('epoch'::text, now()) NOT NULL,
    author text NOT NULL,
    comment text NOT NULL,
    type character varying(20) DEFAULT 'comment'::character varying NOT NULL
);


ALTER TABLE public.rental_moveout_comment OWNER TO portico;

--
-- Name: seq_rental_notification; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_notification
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_notification OWNER TO portico;

--
-- Name: rental_notification; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_notification (
    id integer DEFAULT nextval('public.seq_rental_notification'::regclass) NOT NULL,
    location_id integer,
    account_id integer,
    contract_id integer NOT NULL,
    message text,
    date bigint NOT NULL,
    last_notified bigint,
    recurrence integer DEFAULT 0 NOT NULL,
    deleted boolean DEFAULT false
);


ALTER TABLE public.rental_notification OWNER TO portico;

--
-- Name: seq_rental_notification_workbench; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_notification_workbench
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_notification_workbench OWNER TO portico;

--
-- Name: rental_notification_workbench; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_notification_workbench (
    id integer DEFAULT nextval('public.seq_rental_notification_workbench'::regclass) NOT NULL,
    account_id integer NOT NULL,
    date bigint NOT NULL,
    notification_id integer,
    workbench_message text,
    dismissed boolean
);


ALTER TABLE public.rental_notification_workbench OWNER TO portico;

--
-- Name: seq_rental_party; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_party
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_party OWNER TO portico;

--
-- Name: rental_party; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_party (
    id integer DEFAULT nextval('public.seq_rental_party'::regclass) NOT NULL,
    identifier character varying(255),
    customer_id integer,
    first_name character varying(255),
    last_name character varying(255),
    comment text,
    is_inactive boolean NOT NULL,
    title character varying(255),
    company_name character varying(255),
    department character varying(255),
    address_1 character varying(255),
    address_2 character varying(255),
    postal_code character varying(255),
    place character varying(255),
    phone character varying(255),
    mobile_phone character varying(255),
    fax character varying(255),
    email character varying(255),
    url character varying(255),
    account_number character varying(255),
    reskontro character varying(255),
    location_id integer,
    result_unit_number character varying(255),
    org_enhet_id bigint,
    unit_leader character varying(255)
);


ALTER TABLE public.rental_party OWNER TO portico;

--
-- Name: seq_rental_price_item; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_price_item
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_price_item OWNER TO portico;

--
-- Name: rental_price_item; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_price_item (
    id integer DEFAULT nextval('public.seq_rental_price_item'::regclass) NOT NULL,
    title character varying(255) NOT NULL,
    agresso_id character varying(255),
    is_area boolean DEFAULT true NOT NULL,
    is_inactive boolean DEFAULT false,
    is_adjustable boolean DEFAULT true,
    standard boolean DEFAULT false,
    price numeric(20,2),
    responsibility_id integer NOT NULL,
    type smallint DEFAULT 1 NOT NULL
);


ALTER TABLE public.rental_price_item OWNER TO portico;

--
-- Name: seq_rental_unit; Type: SEQUENCE; Schema: public; Owner: portico
--

CREATE SEQUENCE public.seq_rental_unit
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_rental_unit OWNER TO portico;

--
-- Name: rental_unit; Type: TABLE; Schema: public; Owner: portico
--

CREATE TABLE public.rental_unit (
    id integer DEFAULT nextval('public.seq_rental_unit'::regclass) NOT NULL,
    composite_id integer NOT NULL,
    location_code character varying(50) NOT NULL
);


ALTER TABLE public.rental_unit OWNER TO portico;

--
-- Data for Name: controller_check_item; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_check_item (id, control_item_id, check_list_id) FROM stdin;
\.


--
-- Data for Name: controller_check_item_case; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_check_item_case (id, check_item_id, status, measurement, location_id, location_item_id, descr, user_id, entry_date, modified_date, modified_by, location_code, component_location_id, component_id) FROM stdin;
\.


--
-- Data for Name: controller_check_item_status; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_check_item_status (id, name, open, closed, pending, sorting) FROM stdin;
\.


--
-- Data for Name: controller_check_list; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_check_list (id, control_id, status, comment, deadline, original_deadline, planned_date, completed_date, component_id, serie_id, location_code, location_id, num_open_cases, num_pending_cases, assigned_to, billable_hours) FROM stdin;
\.


--
-- Data for Name: controller_control; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control (id, title, description, start_date, end_date, procedure_id, requirement_id, costresponsibility_id, responsibility_id, control_area_id, repeat_type, repeat_interval, enabled) FROM stdin;
\.


--
-- Data for Name: controller_control_component_list; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_component_list (id, control_id, location_id, component_id, enabled) FROM stdin;
\.


--
-- Data for Name: controller_control_group; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_group (id, group_name, procedure_id, control_area_id, building_part_id, component_location_id, component_criteria) FROM stdin;
\.


--
-- Data for Name: controller_control_group_component_list; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_group_component_list (id, control_group_id, location_id) FROM stdin;
\.


--
-- Data for Name: controller_control_group_list; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_group_list (id, control_id, control_group_id, order_nr) FROM stdin;
\.


--
-- Data for Name: controller_control_item; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_item (id, title, required, what_to_do, how_to_do, control_group_id, control_area_id, type) FROM stdin;
\.


--
-- Data for Name: controller_control_item_list; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_item_list (id, control_id, control_item_id, order_nr) FROM stdin;
\.


--
-- Data for Name: controller_control_item_option; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_item_option (id, option_value, control_item_id) FROM stdin;
\.


--
-- Data for Name: controller_control_location_list; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_location_list (id, control_id, location_code) FROM stdin;
\.


--
-- Data for Name: controller_control_serie; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_serie (id, control_relation_id, control_relation_type, assigned_to, start_date, repeat_type, repeat_interval, service_time, controle_time, enabled) FROM stdin;
\.


--
-- Data for Name: controller_control_serie_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_control_serie_history (id, serie_id, assigned_to, assigned_date) FROM stdin;
\.


--
-- Data for Name: controller_document; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_document (id, name, procedure_id, title, description, type_id) FROM stdin;
\.


--
-- Data for Name: controller_document_types; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_document_types (id, title) FROM stdin;
1	procedures
\.


--
-- Data for Name: controller_procedure; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.controller_procedure (id, title, purpose, responsibility, description, reference, attachment, start_date, end_date, procedure_id, revision_no, revision_date, control_area_id, modified_date, modified_by) FROM stdin;
\.


--
-- Data for Name: fm_action_pending; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_action_pending (id, item_id, location_id, responsible, responsible_type, action_category, action_requested, action_deadline, action_performed, reminder, created_on, created_by, expired_on, expired_by, remark) FROM stdin;
\.


--
-- Data for Name: fm_action_pending_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_action_pending_category (id, num, name, descr) FROM stdin;
1	approval	Approval	Please approve the item requested
2	remind	Remind	This is a reminder of task assigned
3	accept_delivery	Accept delivery	Please accept delivery on this item
\.


--
-- Data for Name: fm_activities; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_activities (id, num, base_descr, unit, ns3420, remarkreq, minperae, billperae, dim_d, descr, branch_id, agreement_group_id) FROM stdin;
\.


--
-- Data for Name: fm_activity_price_index; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_activity_price_index (activity_id, agreement_id, index_count, current_index, this_index, m_cost, w_cost, total_cost, entry_date, index_date, user_id) FROM stdin;
\.


--
-- Data for Name: fm_agreement; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_agreement (group_id, id, vendor_id, contract_id, name, descr, status, entry_date, start_date, end_date, termination_date, category, user_id) FROM stdin;
\.


--
-- Data for Name: fm_agreement_group; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_agreement_group (id, num, descr, status) FROM stdin;
\.


--
-- Data for Name: fm_agreement_status; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_agreement_status (id, descr) FROM stdin;
closed	Closed
active	Active agreement
planning	Planning
\.


--
-- Data for Name: fm_async_method; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_async_method (id, name, data, descr) FROM stdin;
\.


--
-- Data for Name: fm_authorities_demands; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_authorities_demands (id, name, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_b_account; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_b_account (id, category, descr, mva, responsible, active, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_b_account_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_b_account_category (id, descr, active, external_project) FROM stdin;
\.


--
-- Data for Name: fm_branch; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_branch (id, num, descr) FROM stdin;
1	rør	rørlegger
2	maler	maler
3	tomrer	Tømrer
4	renhold	Renhold
\.


--
-- Data for Name: fm_budget; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_budget (id, year, b_account_id, district_id, revision, access, user_id, entry_date, budget_cost, remark, ecodimb, category) FROM stdin;
\.


--
-- Data for Name: fm_budget_basis; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_budget_basis (id, year, b_group, district_id, revision, access, user_id, entry_date, budget_cost, remark, distribute_year, ecodimb, category) FROM stdin;
\.


--
-- Data for Name: fm_budget_cost; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_budget_cost (id, year, month, b_account_id, amount) FROM stdin;
\.


--
-- Data for Name: fm_budget_period; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_budget_period (year, month, b_account_id, percent_, user_id, entry_date, remark) FROM stdin;
\.


--
-- Data for Name: fm_building_part; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_building_part (id, descr, filter_1, filter_2, filter_3, filter_4) FROM stdin;
\.


--
-- Data for Name: fm_cache; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_cache (name, value) FROM stdin;
sql_single_1	eNptTk8LwiAU/yqPnVwMYXSJxU6xoKgJ1V1E3RCahgrRt8+k0WY7vcfvv6vW5abKrs2p2d2gG+jdcOaV0SUOV/bGvoA5CD9VopjydOSxkI7bryoiVLNBzsTYPLW0aQR+MOup6agP9B+5KoRy3ir+qQbYX8gZEAKYrYQjObQRig1AWkCw2FyPGqxEnv980w0L9mRinThiWLZ9AxeCdOE=
uicols_single_1	eNqV0FEOwiAMBuCz2BMM3aJ2l/AGhGyYEN0g0EWXhbtbR0jgyfjWn0K/UIUtbgFFg2Bmt5Ck1WnoFXa4GWz6wH0g/SboDYo6Hut4qmNbxrjXs5rq2VeEpx2ETI0EdAijDoMvhO+dLFwQ7GvWXpoxK4KfOOVJ2rskbu6tWE4qSD48lNyZs9eT8o8MsuC8ddrTmlHeTsb2MqaNBVK0hPTDf4nbLyJ+AJnPe84=
cols_return_single_1	eNo9j1kOgzAMBe+SE5CyFMxhIiuk1GqJUYiEUNW711nUT888vzgIuoEPQTMfMICyGA0taibQAnSbiVs5XMbj5pK4iZhAvdnqP2tTWEv4iX51Jl57xl0pDW7D8Eqgr6WyjJHYG8tLTg4iulKaxruMIyg+vQv1njGt9qB2DNHww0SRVU319YWOGMiWH3x/BqtFCg==
sql_single_2	eNptj10LwiAUhv/KYVcuhmTUzWJXsaCoDap7kelCaDNUiP59zho425WH8374HJOvl5s8uZancneDtqMP1TArVb/C7hV3pd/ADLiZSp6FOh11zIVp9M/lN7RnnQjNBLuJzKzVqxc6aib4ybSlqqXWyfG3eJFxaayWzUAEsL/UZ0AIAUzo4VgfqnBDoK4ATZoGJoAC/kDT9BsfFE/oszBLXoweLHmYC2+YiUcnFlHClyXbD3z3kmI=
uicols_single_2	eNqdkU0OwiAQha9iOUGp1p/pzgMYb0DQoiHWlsA0akzv7rSIgbjS3bx5w7wvjIQFPB3wHJhuTY8CH0axSsIKnhryypHPUN2RVRp4KotUzlO5SGWZymUsh6lu5TVN3gBrumMhvOHjS2C1ckcb5dMMj/LHJyHfb+CfDSPEGlh3a5UVug4gnLYaaVF0J4FkTtYQh0VU1MxiohVpq67SXgITJRjbGWXxEbiodeh1U+v2HNg4sWb799xsFxHSLQLZVA7+Pg4l9s7/2K88+2+e7f88wwv8I6rl
cols_return_single_2	eNo9j+0OgyAMRd+FJ1jxY1ofhjTAHNkEgySLWfbuK6j8vOdAb0sIEr8Ob9OGPQpNSTkjJofAAJpC7BzirjwtNgvJYkTxDhoqayqTlbV5APCAJ/nZqrSvBXdHUbQLxVcG/VnEnym54JUOpry8s2iPohyHGmWOI8cBRfh4G6+V8xHQoVgpJhUeKrG9HJzrGLel6PRx5u8PaZdPpA==
sql_single_3	eNptUF0LwiAU/SuXPW0xBDciWOwhakFRG1TvMvYRQpuhQvTvU2ngnL4o5+Pe4xHZOt1kwb24FPsH9AN5saaWlI0pUnf3ZPwLtQD1JrSNbZ5MPGo70fC/yiBkrIfOFmOkXngBJxpOPGr2GTvuLMToXXNJWE+kot00aBW3VEhOGx0U4HirrhCqAzD7FZyrU2kjCVQlhLNROhRADoukEezKw1KMfWIcRYtd2N2V+Ox4ZteMqcN4wVtTPmkQbW2fXZjH7vSZOw4zLNj+ANY2vqk=
uicols_single_3	eNqd0UsOwiAQBuCrKCcQtT6mOxO3pjcg2KIhKiUwjRrTuzsWMRBXuuOfaWe+gIQ5PDzwCTBtbIcC71axUsIKHhompac+Q3VDVmrgeZzmcZbHeR6LPC7yuExjP5yNvOSQNbBzW89EaARNAaxRvnYJh77hCYfiNOG8JkROGMg/A1+mFbD2apQTuokuTkusdCjag0BqDq0+3Z0gqThOgUvKTl2kO0UibbCutcrhPTKptO/0udHmGKlUUgadNLWKXE78cfX+dbRL0PR4ETsc+/CgHiV2Ptzpr8Tqm7j5Jm7/J/ZPxPq/ZA==
cols_return_single_3	eNo9kGsOgyAQhO/CCeSh1fUwZIPUklYwSGJM07sXUPfnfLPszILAFXwdNOMGHTCDSbuJjQ54BlxWYucQD+1xscUQ2RiAfYLhxCQxQUwRk8TaspTnpS/0s9XpWCvuzvBoF4zvAh5XeH6MyQWvTZjqZJ8NdYYXOZAUtXNDWlZdjuiBhd3beN9V+vMW2Iox6fDUKbu3J69+k9tSdOb8i98fXT1aRA==
sql_single_4	eNp1UtFOwyAU/ZWbPa1maYTuaaYPRrtEM9tE906wUG2ywQIY499bxrpCYX0p95xzz72lR2/Q/RptFh/VrnraQ3ckB9lS00uxzoc3/5LqD6iG4Ux6tvJ5MvI547pVF9UZIYIeuS9G+XBCEYwtjCO4sHCRMJG/gqvZHig/UWWI7IgZ6AupjeLcUMYU13raz8HOeDz/HD+5Cj4sv1DOynBBhcl7Zg1cETIHqp2lFVwLT9D1ylNM1aqVwtDWkNO3FDxc4W7F+mGNvrXDALbvzRss3QMQ/CZ4bV5qHymgqWEZuNnrBCghuuMMHuvnWIxTYnxDjFJilGXRYni+WJGahW/MKlKz8K1ZaD4Lp9pR0G6Zc8LOvZBMXjlqhkD4fX4GE+2ziJazDme2q7b7yTGIcGTphbSM8p5wc1FM2FwTXfpZz7LFwz8NX3IG
uicols_single_4	eNrl0ktugzAQBuCrgE8QA3ngrFqp2yo3QA44rdVgkBnURlHu3hnAYJNV193xz5Dx+AtSZOLeCb4RTJu2hwJurWJHKfhO3LXYHDt8gYH6AXbUgocxCWMaxiyM2zDuwrgP4yGM+WqN9Vq0106wT11VygyV5Kmy2o6v1uPBfo/h2ch6ZZELdm3KrBg7I8hWsEp1pfVE8B3uiWBMPBGMqSdCA53IOJ/P84nlIFjzbZQtdOVoOJ7ZSgtFcykAm1OLmDgXrAOrFCxL0uY8Xep9fVZ2hsvnxjhlsMtJwkgzF9NpO9l5g+lC9OlctPXL2+m8sjEgSyjaz8aoAXWx8lWxGvuie8xW1dJ+OVM0aG3TKgs354qlc6+vlTYfzhZLyoCVplTOF1d+IakaGw6ZI3p8msZF7x41XsURT48DaYLvj0RRaJp5jRAV/4V4xqRb93V9myHJLCbKKKSkXw2WUWhJ50yY0YJJUzqQ0HfjR/tn0tMz6esz6du/IX38AmP4hiA=
cols_return_single_4	eNpNkVtygzAMRffiFWDzCCiL8ajGSTwtNmPU6WQ62Xv8Qu3nPRekI0BQPfw66K4HTCAMknaruDqQCci+EHsP8ak9bjYXKhULiK9gJLOemWI2MOuZjcwGZlNeJNOiB/q71fTcC75UoWg3jJ8ZzE0ovYzkgtcmrOXJJRVDFSriHWdVsuTcl6w4DyVn+RlE+PE2nsdneTmC2DGSDjdNqT27sQkfFK0lvkNOTfDk39uHjaW51LNb0cbMFZL16Bku7QPh8TdY5YNkB+Lm4n/Mfyh4QkN6fwRfG9UMV5dWOlOnv94KeZiV
sql_1_lt_l_f1	eNp1kVELwiAQx7/K0VNGDFbQQ7GnWFCsBtW7mLMQNh0qVN8+LSazOV88//e/38mdXq8Wy/Xkkhf59gr3BteSEsOlSJPZPHjaKB0qWJCGBTK2N3tI9U4qpqkCoqFTsGEv48yK6VYKzW+85uaNlaxZwitndSG2IezO5RGm7gS/AoAi313hUO5PLiGfgikoTxDakq/uQJl32Q4IhdUtUQbLOzbWEIH0057VFyPI4RwiYJ/K4iU/bof8mxaVwhBqItguspaK/djx2tA53sptw/cZQUVK/Kzie0ZosvkAoMnjGg==
uicols_1___f1	eNqdkktugzAQhq9SzQliIG06nKCbqKtukYsniSWwkTFVUMTda+M4YFSpahdoHrb/bx5wPOCtR7ZDkKobbGXHjqDkuMebxF3Z4zPCRQpBCkqJbJvIXKJAsHS1PszTsEivT/Oh4m1KyBxbRHWWIzS65lZqVdVa0Iri8ixSXkNYBbWAekEwuqHKqznWHkFQX5sE5pND246Rt4495oDQGd2RsWNE+ZJi7unIv1TkLW+nMMLecjv0ofu/Md//xXQP6VpfuDqvJ/rpPg8KNnOWzarBFnM+jIc38qySUl0Tscq7m4Ud1qQsmdWOGzo9djxfDfUI7maw/Yd+VmUIb49+ET62am7FJ21abmfwr3J3N1/cRWz6Bmi93D0=
cols_return_1___f1	eNpLtDK1qs60MrAutjKyUspMUbLOtDIEcgyNrZRy8pMTSzLz8+KT81NSQRJGQAkTsLghiGsM5FpCuPF5iblgJSZAMXMrpaL8nNR4kGm1AIa5HWc=
sql_2_lt_l_f1	eNp1klELgjAUhf/KpaeMEPQlMHwKhcISqvexdMZAN9kG5b9vMxzali/enZ3z7e6qTHbxLlndsiI73KHpUMsrrChncbjZLpa6ihwlnivR6EEMd8Q1ujLSb/LkYghrIisBWMKkIEXeypgFkT1nkj5oS9WABG9JSGtjNSXSJeTX8gzr8Vn0DwCn8niZaxGUF1g7t4IUnFsEARRZfrcI/mJEjPGld9RNH6l16QZ/0z0WCvEGKW3wQObbljUXPUh3jA44Du1W6o98uRPyZ9gVZwpXyoOdKm2pyZftzy6d/48yH9Oe8wflidhZ+X+TIFjtP6C8Bx8=
uicols_2___f1	eNqtk0tugzAQhq9S+QSxeSSd7LrLpuqqW+Rgh1gFg8BUQRF3r40ZwGmlqlIXaF7W/N+MDYcD3DugOyBKN73JzNBIcuSwh7uC3bGDFMhVCSE1OSqgjwlmEzEQI2/GhVEYxmGYhGEaNhunouZVqM8smUBtGgEp65wbVessr4XcMNg83TDYkCHDs69mvrkH8Tm25BzNHkhblzJzghYnASJkl7cBj0v2VTUg0jZ2JAcgTVs3sjUD0tjUuVelULpAIjcIHnt65Z8asVwBDy+FNNAZ/X11hpu+88v8G9/bd76X/+WzHeUtv3JdbC/zbD8H5S2zlk4E3sazTWabTuf8NfBSFToY0y4AJ5xd5p9TLrWRLY4WJObnWMrL9jlimGIrzy+43e/jz/Czqmui9AdqUiCnZY9A3pfVzf5WyL7CS91W3EyIvyrNbrS68eomq7tKjF/jliWM
cols_return_2___f1	eNo9jVEKgCAQBe+yJ8jVktbDiKgfgrlQ/UV3zzXwc2Z4vECWnkKLuwgJSgJXSHVQmqByDHfh5iOnLAF7MMMrQT0RBU3H/a++hWMs1ulwuq07S3ByzV4O3w8lryep
sql_1_lt_l_f	eNptj1ELgjAUhf/KwSeNGEjUg+FTKBSmUL5fhk4R0sUcVP++JSQO97TtO+d+l43R7rCPvHuSJacSTU8PWXHdySFkm631NLdwTWjgvbAwmVO0Un1YLcZKgY/4E9LirYH0Vlzh+761DkCWpCUuxTn/BfI1CIUih11jE6euRjy3WFcHgT395EqTbEibgkOyjGfXEjqU6w86xHMUu0cmr3f8AlO5gYg=
uicols_1___f	eNqVkUtqwzAQhq9SdILITks6PkE3patujSpNEoEtGWlcYoLvXj2iIodC24WYmR/x/fMQcICrB74Dps00U0/LhKwTsIerhl3n4QnYWSuFhnUa+L3QBGEPjPBCsWzrck25EeMW2AQrVWC8BTZYKUhb00ursIIGnRfocy77TAvkR2AKvXQbdBTncVwKva4j9ABscnZCR0sBxwaK9vAqPk2ix314EjT7PMr/PN7+5BE+4kWehTnV+/kIL4JzbELkiRJjHlsM+mQ2LYVmSze3tMl3kmgIXXWYAY9UzJUIA95f+2cUB/byPQyw94QIJzlaNwpKFr8ybmlb0nX9AsnTvcs=
cols_return_1___f	eNpLtDKxqs60MrAutjKyUspMUbLOtDIEcgyNrZRy8pMTSzLz8+KT81NSQRJGQAkTsLghiGsM5FpCuPF5iblAJbUAqK4X2Q==
sql_3_lt_l_f	eNptkVELgjAUhf/KxSeNEDahwOghSqEwhfJ9iM4Q0oUOqn+fTZTp3V62++2cc3dZ52/I1rfuQRQcUyhr9hR5JivReO5qPSv7E0GEIuLphCgXa7Ka65gqK8KDH2PW7/wh2q9b8C5vIetgJEzyjwQIb8kV7P+ajQAAl+Qc64xCEoONxoA9oPc5cIhPWEsMWuI4qBNZdqIGNxndURCmU4R4N7xV9rlWcVYVQ4aq3KpYul9ZK5komewFhhD9esrSoSESfwUK9tzpam+2qFxr9wMv9dgI
uicols_3___f	eNq1k01uwyAQha9ScYL4l3Syq9RFN1VX3UbEkATVxpaNq0SV714wjA1JpaqLLqx572Hij2HCYAtfAyQbIFJ1o97rayfIjtlYwmY3QAnkLDkXiuwkJLdBaoIciBYXbW0W2zy2RWzL2NLQTrNWrIlhUoPJESTJgNRtxbRs1b5quQiATJ4EQMamAZCxGQI9upf37luOymXpktEly3xm+AogXAxVHwHacGyaKzKG3qJtgXR924leXxHPRIdR1lyqEyKaSCjdM1UJxLSHxZ0Pr+xTIatdwP3LAvUL+Ct+YXI3PWimx8E1+m/sb/fsT/fsz//Ebttyqc5MncKpOJjHAruamprMdK7mvha+lr7SubprZLU8qagVpknYBS9TN/mVoRI9Hj8K8tvAj3stjuG4o6WhdYfjzFzM7f/vZxS7U6qPcMS9tRgJkJel+UDel357TRc9zYN9bPuG6Rn71y97ma0yX2WxynKVFOU0fQMni1Bk
cols_return_3___f	eNo9zEsOgCAMRdG9sAJp8VcWQwg4aKIw0Jlx71I0HZ77mkZa6GYa/ElAhrPxTLbBIpm9pnhxLSHVvMkAbXC9WyEqQeiUKBwb1+84lHj0B5M20DZrw789L9MKLF8=
sql_2_lt_l_f	eNptkFELgjAQx7/K4ZNGDJQIMnwKhcIUyvdj6AwhXcxB9e0bi2R27mW73/3vx7Ex3mx3sXdN8/RQQdvjXdZcd3KI2Go9K80rJCRySWgzOPBe0CDFaG5xk+rNGjHWCvgIP4JavDRAdinP4JszWwwATuWxcFkIZQE+WRcSIOsFAeRpVk0K+RyEsuPzrOXYNV+HrVjX/E8/uNIoW9QmsCBx25PLhQtK+j9EHLGplSyPWK+3/wCmx6V9
uicols_2___f	eNqlUkFugzAQ/ErlF8QGqnS59dZL1VOvyMEOsQoGgamCIv7etY0TO61UVT2gnRmDZ3ZZDnu4TEB3QJQeZlOZZZCk5PAIFwW7ckJATkoIqUmpgN4LDIUciJFnY2mW0jylRUxXhzXvUjuGQUSwohmQtq+5Ub2u6l7IyBJ1GlkiZcHyyZ9W/nLv6zW2aWheABFyqsfE3Ypz1y0hQMyt7x7IMPaDHM0SvFE6zKoVSjfB38YOrz288k8dQtiD8PJ2sPrhT4abefKT+Vuet+95nv+XB2+Q5/rEdRP/mgM+NoSvDCt1jr7mWy1c9ePlrWp00g42GjrZIPMLVUtt5BhaSIRth1p5jHfIUx9WcBze/dr+bGW/VPojGFEgL9chAXm/zsXh1W3NsR87blyYX6/fYHaD+Q0WAa7rF6sHBwo=
cols_return_2___f	eNpLtDKzqs60MrAutjKyUspMUbLOtDIEcgyNrZRy8pMTSzLz8+KT81NSQRJGQAkTsLghiGsM5xqBuCZAriVENj4vMReswxQuZgQVqwUAjtAiGQ==
sub_query_street_4__	eNrLtDK0BgAC8wEQ
sql_4_lt_l_f	eNp1klFrgzAUhf/KpU86hizRh2HxYawWNjqF1fcQmjiE1owksPXfVyKKMVdfYs495zv3ISZ/pS/57lyeyvcG2hu7qgu3neqz5OnZuw5/JFBooKSBki0V4jis5zfpGdlwyh+l74mQ5qKBG5gUZuW/BTh+118QjZ+3KAB81h/VUkuhriAKVoPCMzkthrfqEHop4qUbXoJ4SRwHW9H1VinSRDeaUqSJbjWRdRNF0mRKn8pjMyPUXy+1i/tep7NOjAx3SzqxTv9ybZlqmR0MCGQ5nllLEUEaq6W0XAgtjQmYWTLOZ5pnR3Dhi0OY86jAI4672z8AJmobjg==
uicols_4___f	eNqdk0FugzAQRa9S+QQBDE0nq1bqopuqq24jBzvEKhhkTJUo4u4dY1xsUqlqF2jmf2T+G9sw2MK1h2QDRKpuMHtz6QTZMbiHq4TNrocCyElyLhTZSUjWRooGBWLE2ViZxZLGMo9lEcpx6hVr4vQUubhPTjIgdVsyI1u1L1suAgL0k4AAZRoQoMwCApTUEzy4tXsXjRg5EC76Ukcc1hya5uJRQm0JtkA63XZCm4unQOswyJpLVXkStIQymqlSeBqMf+yYNg2+8Eh2Tv+1u1f2qSYue0a9YWbo3Y79je7tlu7plu7533R2tHN5YqoKD/CAj0VyNcWaTPmu0rnmcy2m6o6A1bJS0ZA4vp9vblN3G0uEE9oPFhl0beRrY76EtTgaPwdnuMvr3+DndLtSqo/w4s2SxtLmJkBevjcRyPuUhzt8bHXDzMTza+DcZktLlzZf2sK34/gF3vQnCw==
cols_return_4___f	eNpLtDK3qs60MrAutjKyUspMUbLOtDIEcgyNrZRy8pMTSzLz8+KT81NSQRJGQAkTsLghiGsM5xqBuCZwrjGIawrnmoC4ZkCuJURvfF5iLtC8WgDT5CQ2
sql_1_lt_l1_f	eNptj1ELgjAUhf/KwSeNGEjUg+FTKBSmUL5fhk4R0sUcVP++JSQO97TtO+d+l43R7rCPvHuSJacSTU8PWXHdySFkm631NLdwTWjgvbAwmVO0Un1YLcZKgY/4E9LirYH0Vlzh+761DkCWpCUuxTn/BfI1CIUih11jE6euRjy3WFcHgT395EqTbEibgkOyjGfXEjqU6w86xHMUu0cmr3f8AlO5gYg=
uicols_1__1_f	eNqVkUtqwzAQhq9SdILITks6PkE3patujSpNEoEtGWlcYoLvXj2iIodC24WYmR/x/fMQcICrB74Dps00U0/LhKwTsIerhl3n4QnYWSuFhnUa+L3QBGEPjPBCsWzrck25EeMW2AQrVWC8BTZYKUhb00ursIIGnRfocy77TAvkR2AKvXQbdBTncVwKva4j9ABscnZCR0sBxwaK9vAqPk2ix314EjT7PMr/PN7+5BE+4kWehTnV+/kIL4JzbELkiRJjHlsM+mQ2LYVmSze3tMl3kmgIXXWYAY9UzJUIA95f+2cUB/byPQyw94QIJzlaNwpKFr8ybmlb0nX9AsnTvcs=
cols_return_1__1_f	eNpLtDKxqs60MrAutjKyUspMUbLOtDIEcgyNrZRy8pMTSzLz8+KT81NSQRJGQAkTsLghiGsM5FpCuPF5iblAJbUAqK4X2Q==
sql_2_lt_l1_f	eNptkFELgjAQx7/K4ZNGDJQIMnwKhcIUyvdj6AwhXcxB9e0bi2R27mW73/3vx7Ex3mx3sXdN8/RQQdvjXdZcd3KI2Go9K80rJCRySWgzOPBe0CDFaG5xk+rNGjHWCvgIP4JavDRAdinP4JszWwwATuWxcFkIZQE+WRcSIOsFAeRpVk0K+RyEsuPzrOXYNV+HrVjX/E8/uNIoW9QmsCBx25PLhQtK+j9EHLGplSyPWK+3/wCmx6V9
uicols_2__1_f	eNqlUkFugzAQ/ErlF8QGqnS59dZL1VOvyMEOsQoGgamCIv7etY0TO61UVT2gnRmDZ3ZZDnu4TEB3QJQeZlOZZZCk5PAIFwW7ckJATkoIqUmpgN4LDIUciJFnY2mW0jylRUxXhzXvUjuGQUSwohmQtq+5Ub2u6l7IyBJ1GlkiZcHyyZ9W/nLv6zW2aWheABFyqsfE3Ypz1y0hQMyt7x7IMPaDHM0SvFE6zKoVSjfB38YOrz288k8dQtiD8PJ2sPrhT4abefKT+Vuet+95nv+XB2+Q5/rEdRP/mgM+NoSvDCt1jr7mWy1c9ePlrWp00g42GjrZIPMLVUtt5BhaSIRth1p5jHfIUx9WcBze/dr+bGW/VPojGFEgL9chAXm/zsXh1W3NsR87blyYX6/fYHaD+Q0WAa7rF6sHBwo=
cols_return_2__1_f	eNpLtDKzqs60MrAutjKyUspMUbLOtDIEcgyNrZRy8pMTSzLz8+KT81NSQRJGQAkTsLghiGsM5xqBuCZAriVENj4vMReswxQuZgQVqwUAjtAiGQ==
\.


--
-- Data for Name: fm_chapter; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_chapter (id, descr) FROM stdin;
\.


--
-- Data for Name: fm_condition_survey; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_condition_survey (id, title, p_num, p_entity_id, p_cat_id, location_code, loc1, loc2, loc3, loc4, descr, address, status_id, category, coordinator_id, vendor_id, report_date, user_id, entry_date, modified_date, multiplier) FROM stdin;
\.


--
-- Data for Name: fm_condition_survey_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_condition_survey_history (history_id, history_record_id, history_appname, history_owner, history_status, history_new_value, history_old_value, history_timestamp) FROM stdin;
\.


--
-- Data for Name: fm_condition_survey_status; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_condition_survey_status (id, descr, closed, in_progress, delivered, sorting) FROM stdin;
\.


--
-- Data for Name: fm_cron_log; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_cron_log (id, cron, cron_date, process, message) FROM stdin;
\.


--
-- Data for Name: fm_custom; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_custom (id, name, sql_text, entry_date, user_id) FROM stdin;
1	test query	select * from phpgw_accounts	\N	\N
\.


--
-- Data for Name: fm_custom_cols; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_custom_cols (custom_id, id, name, descr, sorting) FROM stdin;
1	1	account_id	ID	1
1	2	account_lid	Lid	2
1	3	account_firstname	First Name	3
1	4	account_lastname	Last Name	4
\.


--
-- Data for Name: fm_custom_menu_items; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_custom_menu_items (id, parent_id, text, url, target, location, local_files, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_district; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_district (id, descr, delivery_address) FROM stdin;
1	District 1	\N
2	District 2	\N
3	District 3	\N
\.


--
-- Data for Name: fm_document; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_document (id, title, document_name, link, descr, version, document_date, entry_date, status, p_num, p_entity_id, p_cat_id, location_code, loc1, loc2, loc3, loc4, address, coordinator, vendor_id, branch_id, category, user_id, access) FROM stdin;
\.


--
-- Data for Name: fm_document_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_document_history (history_id, history_record_id, history_appname, history_owner, history_status, history_new_value, history_old_value, history_timestamp) FROM stdin;
\.


--
-- Data for Name: fm_document_relation; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_document_relation (id, document_id, location_id, location_item_id, entry_date) FROM stdin;
\.


--
-- Data for Name: fm_document_status; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_document_status (id, descr) FROM stdin;
draft	Draft
final	Final
obsolete	obsolete
\.


--
-- Data for Name: fm_eco_period_transition; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_eco_period_transition (id, month, day, hour, remark, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_eco_periodization; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_eco_periodization (id, descr, active) FROM stdin;
\.


--
-- Data for Name: fm_eco_periodization_outline; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_eco_periodization_outline (id, periodization_id, month, value, dividend, divisor, remark) FROM stdin;
\.


--
-- Data for Name: fm_eco_service; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_eco_service (id, name, active) FROM stdin;
\.


--
-- Data for Name: fm_ecoart; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecoart (id, descr) FROM stdin;
1	faktura
2	kreditnota
\.


--
-- Data for Name: fm_ecoavvik; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecoavvik (bilagsnr, belop, fakturadato, forfallsdato, artid, godkjentbelop, spvend_code, oppsynsmannid, saksbehandlerid, budsjettansvarligid, utbetalingid, oppsynsigndato, saksigndato, budsjettsigndato, utbetalingsigndato, overftid) FROM stdin;
\.


--
-- Data for Name: fm_ecobilag; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecobilag (id, bilagsnr, bilagsnr_ut, kidnr, typeid, kildeid, project_id, kostra_id, pmwrkord_code, belop, fakturadato, periode, forfallsdato, fakturanr, spbudact_code, regtid, artid, godkjentbelop, spvend_code, dima, loc1, dimb, mvakode, dimd, dime, oppsynsmannid, saksbehandlerid, budsjettansvarligid, utbetalingid, oppsynsigndato, saksigndato, budsjettsigndato, utbetalingsigndato, merknad, splitt, kreditnota, pre_transfer, item_type, item_id, external_ref, currency, process_log, process_code, periodization, periodization_start, line_text, external_voucher_id) FROM stdin;
\.


--
-- Data for Name: fm_ecobilag_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecobilag_category (id, descr) FROM stdin;
1	Drift, vedlikehold
2	Prosjekt, Kontrakt
3	Prosjekt, Tillegg
4	Prosjekt, LP-stign
5	Administrasjon
\.


--
-- Data for Name: fm_ecobilag_process_code; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecobilag_process_code (id, name, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_ecobilag_process_log; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecobilag_process_log (id, bilagsnr, process_code, process_log, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_ecobilagkilde; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecobilagkilde (id, name, description) FROM stdin;
\.


--
-- Data for Name: fm_ecobilagoverf; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecobilagoverf (id, bilagsnr, bilagsnr_ut, kidnr, typeid, kildeid, project_id, kostra_id, pmwrkord_code, belop, fakturadato, periode, forfallsdato, fakturanr, spbudact_code, regtid, artid, godkjentbelop, spvend_code, dima, loc1, dimb, mvakode, dimd, dime, oppsynsmannid, saksbehandlerid, budsjettansvarligid, utbetalingid, oppsynsigndato, saksigndato, budsjettsigndato, utbetalingsigndato, overftid, ordrebelop, merknad, splitt, filnavn, kreditnota, item_type, item_id, external_ref, currency, process_log, process_code, periodization, periodization_start, manual_record, line_text, external_voucher_id) FROM stdin;
\.


--
-- Data for Name: fm_ecodimb; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecodimb (id, descr, org_unit_id, active) FROM stdin;
\.


--
-- Data for Name: fm_ecodimb_role; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecodimb_role (id, name) FROM stdin;
1	Bestiller
2	Attestant
3	Anviser
\.


--
-- Data for Name: fm_ecodimb_role_user; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecodimb_role_user (id, ecodimb, user_id, role_id, default_user, active_from, active_to, created_on, created_by, expired_on, expired_by) FROM stdin;
\.


--
-- Data for Name: fm_ecodimb_role_user_substitute; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecodimb_role_user_substitute (id, user_id, substitute_user_id) FROM stdin;
\.


--
-- Data for Name: fm_ecodimd; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecodimd (id, descr) FROM stdin;
\.


--
-- Data for Name: fm_ecologg; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecologg (batchid, ecobilagid, status, melding, tid) FROM stdin;
\.


--
-- Data for Name: fm_ecomva; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecomva (id, percent_, descr) FROM stdin;
2	\N	Mva 2
1	\N	Mva 1
0	\N	ingen
3	\N	Mva 3
4	\N	Mva 4
5	\N	Mva 5
\.


--
-- Data for Name: fm_ecouser; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ecouser (id, lid, initials) FROM stdin;
\.


--
-- Data for Name: fm_entity; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_entity (location_id, id, name, descr, location_form, documentation, lookup_entity) FROM stdin;
\.


--
-- Data for Name: fm_entity_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_entity_category (location_id, entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level, location_link_level, fileupload, loc_link, start_project, start_ticket, is_eav, enable_bulk, enable_controller, jasperupload, parent_id, level, org_unit, entity_group_id) FROM stdin;
\.


--
-- Data for Name: fm_entity_group; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_entity_group (id, name, descr, active, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_entity_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_entity_history (history_id, history_record_id, history_appname, history_attrib_id, history_owner, history_status, history_new_value, history_old_value, history_timestamp) FROM stdin;
\.


--
-- Data for Name: fm_entity_lookup; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_entity_lookup (entity_id, location, type) FROM stdin;
\.


--
-- Data for Name: fm_event; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_event (id, location_id, location_item_id, attrib_id, responsible_id, action_id, descr, start_date, end_date, repeat_type, repeat_day, repeat_interval, enabled, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_event_action; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_event_action (id, name, action, data, descr, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_event_exception; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_event_exception (event_id, exception_time, descr, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_event_receipt; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_event_receipt (event_id, receipt_time, descr, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_event_schedule; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_event_schedule (event_id, schedule_time, descr, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_external_project; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_external_project (id, name, budget, active) FROM stdin;
\.


--
-- Data for Name: fm_gab_location; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_gab_location (location_code, gab_id, user_id, entry_date, loc1, loc2, loc3, loc4, address, split, remark, owner, spredning) FROM stdin;
\.


--
-- Data for Name: fm_generic_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_generic_history (history_id, history_record_id, history_owner, history_status, history_new_value, history_old_value, history_timestamp, history_attrib_id, location_id, app_id) FROM stdin;
\.


--
-- Data for Name: fm_idgenerator; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_idgenerator (name, start_date, value, increment, descr) FROM stdin;
Bilagsnummer	0	2003100000	\N	Bilagsnummer
bilagsnr_ut	0	0	\N	Bilagsnummer utgående
Ecobatchid	0	1	\N	Ecobatchid
project	0	1000	\N	project
Statuslog	0	1	\N	Statuslog
workorder	0	1000	\N	workorder
request	0	1000	\N	request
\.


--
-- Data for Name: fm_investment; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_investment (entity_id, invest_id, entity_type, p_num, p_entity_id, p_cat_id, location_code, loc1, loc2, loc3, loc4, address, descr, writeoff_year) FROM stdin;
\.


--
-- Data for Name: fm_investment_value; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_investment_value (entity_id, invest_id, index_count, current_index, this_index, initial_value, value, index_date) FROM stdin;
\.


--
-- Data for Name: fm_jasper; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_jasper (id, location_id, title, descr, formats, version, access, user_id, entry_date, modified_by, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_jasper_format_type; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_jasper_format_type (id) FROM stdin;
PDF
CSV
XLS
XHTML
DOCX
\.


--
-- Data for Name: fm_jasper_input; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_jasper_input (id, jasper_id, input_type_id, is_id, name, descr) FROM stdin;
\.


--
-- Data for Name: fm_jasper_input_type; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_jasper_input_type (id, name, descr) FROM stdin;
1	integer	Integer
2	float	Float
3	text	Text
4	date	Date
5	timestamp	timestamp
6	AB	Address book
7	VENDOR	Vendor
8	user	system user
\.


--
-- Data for Name: fm_key_loc; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_key_loc (id, num, descr) FROM stdin;
\.


--
-- Data for Name: fm_location1; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location1 (id, location_code, loc1, loc1_name, part_of_town_id, entry_date, category, user_id, owner_id, status, mva, remark, kostra_id, change_type, rental_area, area_gross, area_net, area_usable, delivery_address, modified_by, modified_on) FROM stdin;
1	5000	5000	Location name	1	\N	1	6	1	1	\N	remark	\N	\N	0.00	0.00	0.00	0.00	\N	\N	2018-01-12 12:30:10.053485
\.


--
-- Data for Name: fm_location1_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location1_category (id, descr) FROM stdin;
1	SOMETHING
99	not active
\.


--
-- Data for Name: fm_location1_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location1_history (id, location_code, loc1, loc1_name, part_of_town_id, entry_date, category, user_id, owner_id, status, mva, remark, kostra_id, change_type, rental_area, area_gross, area_net, area_usable, delivery_address, exp_date, modified_by, modified_on) FROM stdin;
\.


--
-- Data for Name: fm_location2; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location2 (id, location_code, loc1, loc2, loc2_name, entry_date, category, user_id, status, remark, change_type, rental_area, area_gross, area_net, area_usable, modified_by, modified_on) FROM stdin;
2	5000-01	5000	01	Location name	\N	1	6	1	remark	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
\.


--
-- Data for Name: fm_location2_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location2_category (id, descr) FROM stdin;
1	SOMETHING
99	not active
\.


--
-- Data for Name: fm_location2_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location2_history (id, location_code, loc1, loc2, loc2_name, entry_date, category, user_id, status, remark, change_type, rental_area, area_gross, area_net, area_usable, exp_date, modified_by, modified_on) FROM stdin;
\.


--
-- Data for Name: fm_location3; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location3 (id, location_code, loc1, loc2, loc3, loc3_name, entry_date, category, user_id, status, remark, change_type, rental_area, area_gross, area_net, area_usable, modified_by, modified_on) FROM stdin;
3	5000-01-01	5000	01	01	entrance name1	1087745654	1	6	1	\N	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
4	5000-01-02	5000	01	02	entrance name2	1087745654	1	6	1	\N	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
5	5000-01-03	5000	01	03	entrance name3	1087745654	1	6	1	\N	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
\.


--
-- Data for Name: fm_location3_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location3_category (id, descr) FROM stdin;
1	SOMETHING
99	not active
\.


--
-- Data for Name: fm_location3_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location3_history (id, location_code, loc1, loc2, loc3, loc3_name, entry_date, category, user_id, status, remark, change_type, rental_area, area_gross, area_net, area_usable, exp_date, modified_by, modified_on) FROM stdin;
\.


--
-- Data for Name: fm_location4; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location4 (id, location_code, loc1, loc2, loc3, loc4, loc4_name, entry_date, category, street_id, street_number, user_id, tenant_id, status, remark, change_type, rental_area, area_gross, area_net, area_usable, modified_by, modified_on) FROM stdin;
6	5000-01-01-001	5000	01	01	001	apartment name1	1087745753	1	1	1A	6	1	1	\N	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
7	5000-01-01-002	5000	01	01	002	apartment name2	1087745753	1	1	1B	6	2	1	\N	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
8	5000-01-02-001	5000	01	02	001	apartment name3	1087745753	1	1	2A	6	3	1	\N	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
9	5000-01-02-002	5000	01	02	002	apartment name4	1087745753	1	1	2B	6	4	1	\N	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
10	5000-01-03-001	5000	01	03	001	apartment name5	1087745753	1	1	3A	6	5	1	\N	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
11	5000-01-03-002	5000	01	03	002	apartment name6	1087745753	1	1	3B	6	6	1	\N	\N	0.00	0.00	0.00	0.00	\N	2018-01-12 12:30:10.053485
\.


--
-- Data for Name: fm_location4_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location4_category (id, descr) FROM stdin;
1	SOMETHING
99	not active
\.


--
-- Data for Name: fm_location4_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location4_history (id, location_code, loc1, loc2, loc3, loc4, loc4_name, entry_date, category, street_id, street_number, user_id, tenant_id, status, remark, change_type, rental_area, area_gross, area_net, area_usable, exp_date, modified_by, modified_on) FROM stdin;
\.


--
-- Data for Name: fm_location_config; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location_config (column_name, location_type, input_text, lookup_form, f_key, ref_to_category, query_value, reference_table, reference_id, datatype, precision_, scale, default_value, nullable) FROM stdin;
tenant_id	4	\N	1	1	\N	0	fm_tenant	id	int	4	\N	\N	True
street_id	4	\N	1	1	\N	1	fm_streetaddress	id	int	4	\N	\N	True
owner_id	1	\N	\N	1	1	\N	fm_owner	id	int	4	\N	\N	True
part_of_town_id	1	\N	\N	1	\N	\N	fm_part_of_town	id	int	4	\N	\N	True
\.


--
-- Data for Name: fm_location_contact; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location_contact (id, contact_id, location_code, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_location_exception; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location_exception (id, location_code, severity_id, category_id, category_text_id, descr, start_date, end_date, reference, alert_vendor, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_location_exception_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location_exception_category (id, name, parent_id) FROM stdin;
\.


--
-- Data for Name: fm_location_exception_category_text; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location_exception_category_text (id, category_id, content) FROM stdin;
\.


--
-- Data for Name: fm_location_exception_severity; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location_exception_severity (id, name) FROM stdin;
\.


--
-- Data for Name: fm_location_type; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_location_type (id, name, descr, pk, ix, uc, list_info, list_address, list_documents, enable_controller) FROM stdin;
1	property	Property	loc1	location_code	\N	a:1:{i:1;s:1:"1";}	\N	\N	\N
2	building	Building	loc1,loc2	location_code	\N	a:2:{i:1;s:1:"1";i:2;s:1:"2";}	\N	\N	\N
3	entrance	Entrance	loc1,loc2,loc3	location_code	\N	a:3:{i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";}	\N	\N	\N
4	Apartment	Apartment	loc1,loc2,loc3,loc4	location_code	\N	a:1:{i:1;s:1:"1";}	\N	\N	\N
\.


--
-- Data for Name: fm_locations; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_locations (id, level, location_code, loc1, name) FROM stdin;
1	1	5000	5000	Location name
2	2	5000-01	5000	Location name, Location name
3	3	5000-01-01	5000	Location name, Location name, entrance name1
4	3	5000-01-02	5000	Location name, Location name, entrance name2
5	3	5000-01-03	5000	Location name, Location name, entrance name3
6	4	5000-01-01-001	5000	Location name, Location name, entrance name1, apartment name1
7	4	5000-01-01-002	5000	Location name, Location name, entrance name1, apartment name2
8	4	5000-01-02-001	5000	Location name, Location name, entrance name2, apartment name3
9	4	5000-01-02-002	5000	Location name, Location name, entrance name2, apartment name4
10	4	5000-01-03-001	5000	Location name, Location name, entrance name3, apartment name5
11	4	5000-01-03-002	5000	Location name, Location name, entrance name3, apartment name6
\.


--
-- Data for Name: fm_ns3420; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_ns3420 (id, num, parent_id, enhet, tekst1, tekst2, tekst3, tekst4, tekst5, tekst6, type) FROM stdin;
1	D00	\N	RS	RIGGING, KLARGJØRING	\N	\N	\N	\N	\N	\N
2	D20	\N	RS	RIGGING, ANLEGGSTOMT	TILFØRSEL- OG FORSYNINGSANLEGG	\N	\N	\N	\N	\N
\.


--
-- Data for Name: fm_order_dim1; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_order_dim1 (id, num, descr) FROM stdin;
\.


--
-- Data for Name: fm_order_template; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_order_template (id, name, content, public_, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_orders; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_orders (id, type) FROM stdin;
\.


--
-- Data for Name: fm_org_unit; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_org_unit (id, parent_id, name, active, created_on, created_by, modified_by, modified_on) FROM stdin;
\.


--
-- Data for Name: fm_owner; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_owner (id, abid, org_name, contact_name, category, member_of, remark, entry_date, owner_id) FROM stdin;
1	1	demo-owner 1	\N	1	\N	\N	\N	\N
\.


--
-- Data for Name: fm_owner_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_owner_category (id, descr) FROM stdin;
1	Owner category 1
\.


--
-- Data for Name: fm_part_of_town; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_part_of_town (id, name, district_id, delivery_address) FROM stdin;
1	Part of town 1	1	\N
\.


--
-- Data for Name: fm_project; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_project (id, parent_id, project_type_id, name, user_id, access, category, entry_date, start_date, end_date, coordinator, status, descr, budget, reserve, p_num, p_entity_id, p_cat_id, location_code, loc1, loc2, loc3, loc4, address, tenant_id, contact_phone, key_fetch, key_deliver, other_branch, key_responsible, external_project_id, planned_cost, account_id, ecodimb, contact_id, account_group, b_account_id, inherit_location, periodization_id, delivery_address) FROM stdin;
\.


--
-- Data for Name: fm_project_budget; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_project_budget (project_id, year, month, budget, order_amount, closed, active, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_project_buffer_budget; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_project_buffer_budget (id, year, month, buffer_project_id, entry_date, amount_in, from_project, amount_out, to_project, active, user_id, remark) FROM stdin;
\.


--
-- Data for Name: fm_project_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_project_history (history_id, history_record_id, history_appname, history_owner, history_status, history_new_value, history_old_value, history_timestamp) FROM stdin;
\.


--
-- Data for Name: fm_project_status; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_project_status (id, descr, approved, closed) FROM stdin;
\.


--
-- Data for Name: fm_projectbranch; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_projectbranch (project_id, branch_id) FROM stdin;
\.


--
-- Data for Name: fm_regulations; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_regulations (id, parent_id, name, descr, external_ref, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_request; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_request (id, condition_survey_id, title, project_id, p_num, p_entity_id, p_cat_id, location_code, loc1, loc2, loc3, loc4, descr, category, owner, access, floor, address, tenant_id, contact_phone, entry_date, amount_investment, amount_operation, amount_potential_grants, status, branch_id, coordinator, responsible_unit, authorities_demands, score, recommended_year, start_date, end_date, building_part, closed_date, in_progress_date, delivered_date, regulations, multiplier) FROM stdin;
\.


--
-- Data for Name: fm_request_condition; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_request_condition (request_id, condition_type, reference, degree, probability, consequence, user_id, entry_date) FROM stdin;
\.


--
-- Data for Name: fm_request_condition_type; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_request_condition_type (id, name, descr, priority_key) FROM stdin;
1	safety	\N	10
2	aesthetics	\N	2
3	indoor climate	\N	5
4	consequential damage	\N	5
5	user gratification	\N	4
6	residential environment	\N	6
\.


--
-- Data for Name: fm_request_consume; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_request_consume (id, request_id, amount, date, user_id, entry_date, descr) FROM stdin;
\.


--
-- Data for Name: fm_request_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_request_history (history_id, history_record_id, history_appname, history_owner, history_status, history_new_value, history_old_value, history_timestamp) FROM stdin;
\.


--
-- Data for Name: fm_request_planning; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_request_planning (id, request_id, amount, date, user_id, entry_date, descr) FROM stdin;
\.


--
-- Data for Name: fm_request_responsible_unit; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_request_responsible_unit (id, name, descr) FROM stdin;
\.


--
-- Data for Name: fm_request_status; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_request_status (id, descr, closed, in_progress, delivered, sorting) FROM stdin;
request	Request	\N	\N	\N	\N
canceled	Canceled	\N	\N	\N	\N
closed	avsluttet	\N	\N	\N	\N
\.


--
-- Data for Name: fm_response_template; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_response_template (id, name, content, public_, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_responsibility; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_responsibility (id, name, descr, created_on, created_by) FROM stdin;
\.


--
-- Data for Name: fm_responsibility_contact; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_responsibility_contact (id, responsibility_role_id, contact_id, location_code, p_num, p_entity_id, p_cat_id, priority, active_from, active_to, created_on, created_by, expired_on, expired_by, remark) FROM stdin;
\.


--
-- Data for Name: fm_responsibility_module; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_responsibility_module (responsibility_id, location_id, cat_id, active, created_on, created_by) FROM stdin;
\.


--
-- Data for Name: fm_responsibility_role; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_responsibility_role (id, name, remark, location_level, responsibility_id, appname, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_s_agreement; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_s_agreement (id, vendor_id, name, descr, status, category, member_of, entry_date, start_date, end_date, termination_date, user_id, actual_cost, account_id) FROM stdin;
\.


--
-- Data for Name: fm_s_agreement_budget; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_s_agreement_budget (agreement_id, year, budget_account, ecodimb, category, budget, actual_cost, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_s_agreement_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_s_agreement_category (id, descr) FROM stdin;
\.


--
-- Data for Name: fm_s_agreement_detail; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_s_agreement_detail (agreement_id, id, location_code, address, p_num, p_entity_id, p_cat_id, descr, unit, quantity, frequency, user_id, entry_date, test, cost) FROM stdin;
\.


--
-- Data for Name: fm_s_agreement_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_s_agreement_history (history_id, history_record_id, history_appname, history_detail_id, history_attrib_id, history_owner, history_status, history_new_value, history_old_value, history_timestamp) FROM stdin;
\.


--
-- Data for Name: fm_s_agreement_pricing; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_s_agreement_pricing (agreement_id, item_id, id, current_index, this_index, cost, index_date, user_id, entry_date) FROM stdin;
\.


--
-- Data for Name: fm_standard_unit; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_standard_unit (id, name, descr) FROM stdin;
1	mm	Millimeter
2	m	Meter
3	m2	Square meters
4	m3	Cubic meters
5	km	Kilometre
6	Stk	Stk
7	kg	Kilogram
8	tonn	Tonn
9	h	Hours
10	RS	Round Sum
\.


--
-- Data for Name: fm_streetaddress; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_streetaddress (id, descr) FROM stdin;
1	street name 1
\.


--
-- Data for Name: fm_template; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_template (id, name, descr, owner, chapter_id, entry_date) FROM stdin;
\.


--
-- Data for Name: fm_template_hours; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_template_hours (id, template_id, record, owner, activity_id, activity_num, grouping_id, grouping_descr, hours_descr, remark, billperae, vendor_id, unit, ns3420_id, tolerance, building_part, quantity, cost, dim_d, entry_date) FROM stdin;
\.


--
-- Data for Name: fm_tenant; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tenant (id, member_of, entry_date, first_name, last_name, contact_phone, contact_email, category, phpgw_account_id, account_lid, account_pwd, account_status, owner_id) FROM stdin;
1	\N	\N	First name1	Last name1	\N	\N	1	\N	\N	\N	1	\N
2	\N	\N	First name2	Last name2	\N	\N	2	\N	\N	\N	1	\N
3	\N	\N	First name3	Last name3	\N	\N	1	\N	\N	\N	1	\N
4	\N	\N	First name4	Last name4	\N	\N	2	\N	\N	\N	1	\N
5	\N	\N	First name5	Last name5	\N	\N	1	\N	\N	\N	1	\N
6	\N	\N	First name6	Last name6	\N	\N	2	\N	\N	\N	1	\N
\.


--
-- Data for Name: fm_tenant_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tenant_category (id, descr) FROM stdin;
1	male
2	female
3	organization
\.


--
-- Data for Name: fm_tenant_claim; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tenant_claim (id, project_id, tenant_id, amount, b_account_id, category, status, remark, user_id, entry_date) FROM stdin;
\.


--
-- Data for Name: fm_tenant_claim_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tenant_claim_category (id, descr) FROM stdin;
\.


--
-- Data for Name: fm_tenant_claim_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tenant_claim_history (history_id, history_record_id, history_appname, history_owner, history_status, history_new_value, history_old_value, history_timestamp) FROM stdin;
\.


--
-- Data for Name: fm_tts_budget; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tts_budget (id, ticket_id, amount, period, remark, created_on, created_by) FROM stdin;
\.


--
-- Data for Name: fm_tts_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tts_history (history_id, history_record_id, history_appname, history_owner, history_status, history_new_value, history_old_value, history_timestamp, publish) FROM stdin;
1	1	tts	1002	O	1517584417		2018-02-02 15:13:37	\N
2	1	tts	1002	S	Message 1 with high priority		2018-02-02 15:14:41	\N
3	2	tts	1002	O	1517584533		2018-02-02 15:15:33	\N
4	2	tts	1002	S	Message 2 with medium priority		2018-02-02 15:16:26	\N
5	3	tts	1002	O	1517584650		2018-02-02 15:17:30	\N
\.


--
-- Data for Name: fm_tts_payments; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tts_payments (id, ticket_id, amount, period, remark, created_on, created_by) FROM stdin;
\.


--
-- Data for Name: fm_tts_priority; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tts_priority (id, name) FROM stdin;
1	1 - Highest
2	2
3	3 - Lowest
\.


--
-- Data for Name: fm_tts_status; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tts_status (id, name, color, closed, approved, in_progress, delivered, actual_cost, sorting) FROM stdin;
\.


--
-- Data for Name: fm_tts_tickets; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tts_tickets (id, group_id, priority, user_id, assignedto, subject, cat_id, billable_hours, billable_rate, status, details, location_code, p_num, p_entity_id, p_cat_id, loc1, loc2, loc3, loc4, floor, address, contact_phone, contact_email, tenant_id, entry_date, finnish_date, finnish_date2, contact_id, order_id, ordered_by, vendor_id, contract_id, tax_code, external_project_id, unspsc_code, service_id, order_descr, b_account_id, ecodimb, budget, actual_cost, actual_cost_year, order_cat_id, building_part, order_dim1, publish_note, branch_id, modified_date, order_sent, order_received, order_received_amount, mail_recipients, file_attachments, delivery_address, continuous, order_deadline, order_deadline2, invoice_remark) FROM stdin;
1	1000	1	1002	\N	Message 1 with high priority	4	\N	\N	O	Details of message 1	5000-01	\N	\N	\N	5000	01	\N	\N	\N	Location name	\N	\N	\N	1517584417	0	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0.00	\N	\N	\N	\N	1	\N	1517584481	\N	\N	0.00	\N	\N	\N	\N	\N	\N	\N
2	1000	2	1002	\N	Message 2 with medium priority	5	\N	\N	O	Message 2	5000-01	\N	\N	\N	5000	01	\N	\N	\N	Location name	\N	\N	\N	1517584533	0	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0.00	\N	\N	\N	\N	1	\N	1517584586	\N	\N	0.00	\N	\N	\N	\N	\N	\N	\N
3	1000	3	1002	\N	Message 3 with low priority	4	\N	\N	O	Message 3	5000-01	\N	\N	\N	5000	01	\N	\N	\N	Location name	\N	\N	\N	1517584650	0	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0.00	\N	\N	\N	\N	1	\N	1517584650	\N	\N	0.00	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: fm_tts_views; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_tts_views (id, account_id, "time") FROM stdin;
1	1002	1517584417
2	1002	1517584547
3	1002	1517584650
\.


--
-- Data for Name: fm_unspsc_code; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_unspsc_code (id, name) FROM stdin;
\.


--
-- Data for Name: fm_vendor; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_vendor (id, entry_date, org_name, email, contact_phone, klasse, member_of, category, mva, owner_id, active) FROM stdin;
1	1515760210	Demo vendor	demo@vendor.org	5555555	\N	\N	1	\N	\N	1
\.


--
-- Data for Name: fm_vendor_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_vendor_category (id, descr) FROM stdin;
1	kateogory 1
\.


--
-- Data for Name: fm_view_dataset; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_view_dataset (id, view_name, dataset_name, owner_id, entry_date) FROM stdin;
\.


--
-- Data for Name: fm_view_dataset_report; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_view_dataset_report (id, dataset_id, report_name, report_definition, owner_id, entry_date) FROM stdin;
\.


--
-- Data for Name: fm_wo_h_deviation; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_wo_h_deviation (workorder_id, hour_id, id, amount, descr, entry_date) FROM stdin;
\.


--
-- Data for Name: fm_wo_hours; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_wo_hours (id, record, owner, workorder_id, activity_id, activity_num, grouping_id, grouping_descr, entry_date, hours_descr, remark, billperae, vendor_id, unit, ns3420_id, tolerance, building_part, quantity, cost, dim_d, category, cat_per_cent) FROM stdin;
\.


--
-- Data for Name: fm_wo_hours_category; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_wo_hours_category (id, descr) FROM stdin;
\.


--
-- Data for Name: fm_workorder; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_workorder (id, num, project_id, user_id, access, category, chapter_id, entry_date, start_date, end_date, tender_deadline, tender_received, inspection_on_completion, coordinator, vendor_id, status, descr, title, budget, calculation, combined_cost, deviation, act_mtrl_cost, act_vendor_cost, actual_cost, addition, rig_addition, account_id, key_fetch, key_deliver, integration, charge_tenant, claim_issued, paid, ecodimb, p_num, p_entity_id, p_cat_id, location_code, address, tenant_id, contact_phone, paid_percent, event_id, billable_hours, contract_sum, approved, mail_recipients, continuous, fictive_periodization, contract_id, tax_code, unspsc_code, service_id, building_part, order_dim1, order_sent, order_received, order_received_amount, delivery_address, file_attachments) FROM stdin;
\.


--
-- Data for Name: fm_workorder_budget; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_workorder_budget (order_id, year, month, budget, contract_sum, combined_cost, active, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: fm_workorder_history; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_workorder_history (history_id, history_record_id, history_appname, history_owner, history_status, history_new_value, history_old_value, history_timestamp) FROM stdin;
\.


--
-- Data for Name: fm_workorder_status; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.fm_workorder_status (id, descr, approved, in_progress, delivered, closed, canceled) FROM stdin;
active	Active	\N	\N	\N	\N	\N
ordered	Ordered	\N	\N	\N	\N	\N
request	Request	\N	\N	\N	\N	\N
closed	Closed	\N	\N	\N	\N	\N
\.


--
-- Data for Name: phpgw_access_log; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_access_log (sessionid, loginid, ip, li, lo, account_id) FROM stdin;
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515491600	0	1002
bad login or password                                           	sysadmin	10.0.2.2	1515661010	0	0
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515661016	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515761058	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1516087534	0	1002
bad login or password                                           	jp641	10.0.2.2	1516361586	0	0
lgol7l5vfv2qdtic2i3amugn83                                      	sysadmin	10.0.2.2	1516361636	0	1002
antevm5jinpes6anu71pve0ci2                                      	sysadmin	10.0.2.2	1516365293	0	1002
jr492ofqtcben7gcgdc7ofpg66                                      	sysadmin	10.0.2.2	1516365301	0	1002
ihgm6th0hjek1na57pdgk5rfq7                                      	sysadmin	10.0.2.2	1516365316	0	1002
31ijm8icf0hh2f8fepft7m19i2                                      	sysadmin	10.0.2.2	1516365387	0	1002
pa18sju96ck15ajq1hu8e07mk5                                      	sysadmin	10.0.2.2	1516365429	0	1002
5v2p2utt6u4c8ku63drvqmmsr1                                      	sysadmin	10.0.2.2	1516365669	0	1002
8rah5ep5652qat006mi2mb6ss3                                      	sysadmin	10.0.2.2	1516366728	0	1002
4vodgkvmhv6nmif11aiaohjjn5                                      	sysadmin	::1	1516392399	0	1002
grrn68o76lgivgjqoc03h95lg0                                      	sysadmin	::1	1516568020	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515491600	0	1002
bad login or password                                           	sysadmin	10.0.2.2	1515661010	0	0
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515661016	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515761058	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1516087534	0	1002
bad login or password                                           	jp641	10.0.2.2	1516361586	0	0
lgol7l5vfv2qdtic2i3amugn83                                      	sysadmin	10.0.2.2	1516361636	0	1002
antevm5jinpes6anu71pve0ci2                                      	sysadmin	10.0.2.2	1516365293	0	1002
jr492ofqtcben7gcgdc7ofpg66                                      	sysadmin	10.0.2.2	1516365301	0	1002
ihgm6th0hjek1na57pdgk5rfq7                                      	sysadmin	10.0.2.2	1516365316	0	1002
31ijm8icf0hh2f8fepft7m19i2                                      	sysadmin	10.0.2.2	1516365387	0	1002
pa18sju96ck15ajq1hu8e07mk5                                      	sysadmin	10.0.2.2	1516365429	0	1002
5v2p2utt6u4c8ku63drvqmmsr1                                      	sysadmin	10.0.2.2	1516365669	0	1002
8rah5ep5652qat006mi2mb6ss3                                      	sysadmin	10.0.2.2	1516366728	0	1002
4vodgkvmhv6nmif11aiaohjjn5                                      	sysadmin	::1	1516392399	0	1002
2h7tl3520askr57shr9pdnr412                                      	sysadmin	::1	1516568027	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515491600	0	1002
bad login or password                                           	sysadmin	10.0.2.2	1515661010	0	0
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515661016	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515761058	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1516087534	0	1002
bad login or password                                           	jp641	10.0.2.2	1516361586	0	0
lgol7l5vfv2qdtic2i3amugn83                                      	sysadmin	10.0.2.2	1516361636	0	1002
antevm5jinpes6anu71pve0ci2                                      	sysadmin	10.0.2.2	1516365293	0	1002
jr492ofqtcben7gcgdc7ofpg66                                      	sysadmin	10.0.2.2	1516365301	0	1002
ihgm6th0hjek1na57pdgk5rfq7                                      	sysadmin	10.0.2.2	1516365316	0	1002
31ijm8icf0hh2f8fepft7m19i2                                      	sysadmin	10.0.2.2	1516365387	0	1002
pa18sju96ck15ajq1hu8e07mk5                                      	sysadmin	10.0.2.2	1516365429	0	1002
5v2p2utt6u4c8ku63drvqmmsr1                                      	sysadmin	10.0.2.2	1516365669	0	1002
8rah5ep5652qat006mi2mb6ss3                                      	sysadmin	10.0.2.2	1516366728	0	1002
4vodgkvmhv6nmif11aiaohjjn5                                      	sysadmin	::1	1516392399	0	1002
gcsv8hrgvtkac6km2gts91h1c0                                      	sysadmin	::1	1516568033	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515491600	0	1002
bad login or password                                           	sysadmin	10.0.2.2	1515661010	0	0
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515661016	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515761058	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1516087534	0	1002
bad login or password                                           	jp641	10.0.2.2	1516361586	0	0
lgol7l5vfv2qdtic2i3amugn83                                      	sysadmin	10.0.2.2	1516361636	0	1002
antevm5jinpes6anu71pve0ci2                                      	sysadmin	10.0.2.2	1516365293	0	1002
jr492ofqtcben7gcgdc7ofpg66                                      	sysadmin	10.0.2.2	1516365301	0	1002
ihgm6th0hjek1na57pdgk5rfq7                                      	sysadmin	10.0.2.2	1516365316	0	1002
31ijm8icf0hh2f8fepft7m19i2                                      	sysadmin	10.0.2.2	1516365387	0	1002
pa18sju96ck15ajq1hu8e07mk5                                      	sysadmin	10.0.2.2	1516365429	0	1002
5v2p2utt6u4c8ku63drvqmmsr1                                      	sysadmin	10.0.2.2	1516365669	0	1002
8rah5ep5652qat006mi2mb6ss3                                      	sysadmin	10.0.2.2	1516366728	0	1002
4vodgkvmhv6nmif11aiaohjjn5                                      	sysadmin	::1	1516392399	0	1002
jsnuv2gp6ak3uju9amp98qd0p1                                      	sysadmin	::1	1516568037	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515491600	0	1002
bad login or password                                           	sysadmin	10.0.2.2	1515661010	0	0
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515661016	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515761058	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1516087534	0	1002
bad login or password                                           	jp641	10.0.2.2	1516361586	0	0
lgol7l5vfv2qdtic2i3amugn83                                      	sysadmin	10.0.2.2	1516361636	0	1002
antevm5jinpes6anu71pve0ci2                                      	sysadmin	10.0.2.2	1516365293	0	1002
jr492ofqtcben7gcgdc7ofpg66                                      	sysadmin	10.0.2.2	1516365301	0	1002
ihgm6th0hjek1na57pdgk5rfq7                                      	sysadmin	10.0.2.2	1516365316	0	1002
31ijm8icf0hh2f8fepft7m19i2                                      	sysadmin	10.0.2.2	1516365387	0	1002
pa18sju96ck15ajq1hu8e07mk5                                      	sysadmin	10.0.2.2	1516365429	0	1002
5v2p2utt6u4c8ku63drvqmmsr1                                      	sysadmin	10.0.2.2	1516365669	0	1002
8rah5ep5652qat006mi2mb6ss3                                      	sysadmin	10.0.2.2	1516366728	0	1002
4vodgkvmhv6nmif11aiaohjjn5                                      	sysadmin	::1	1516392399	0	1002
grrn68o76lgivgjqoc03h95lg0                                      	sysadmin	::1	1516568020	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515491600	0	1002
bad login or password                                           	sysadmin	10.0.2.2	1515661010	0	0
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515661016	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515761058	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1516087534	0	1002
bad login or password                                           	jp641	10.0.2.2	1516361586	0	0
lgol7l5vfv2qdtic2i3amugn83                                      	sysadmin	10.0.2.2	1516361636	0	1002
antevm5jinpes6anu71pve0ci2                                      	sysadmin	10.0.2.2	1516365293	0	1002
jr492ofqtcben7gcgdc7ofpg66                                      	sysadmin	10.0.2.2	1516365301	0	1002
ihgm6th0hjek1na57pdgk5rfq7                                      	sysadmin	10.0.2.2	1516365316	0	1002
31ijm8icf0hh2f8fepft7m19i2                                      	sysadmin	10.0.2.2	1516365387	0	1002
pa18sju96ck15ajq1hu8e07mk5                                      	sysadmin	10.0.2.2	1516365429	0	1002
5v2p2utt6u4c8ku63drvqmmsr1                                      	sysadmin	10.0.2.2	1516365669	0	1002
8rah5ep5652qat006mi2mb6ss3                                      	sysadmin	10.0.2.2	1516366728	0	1002
4vodgkvmhv6nmif11aiaohjjn5                                      	sysadmin	::1	1516392399	0	1002
2h7tl3520askr57shr9pdnr412                                      	sysadmin	::1	1516568027	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515491600	0	1002
bad login or password                                           	sysadmin	10.0.2.2	1515661010	0	0
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515661016	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515761058	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1516087534	0	1002
bad login or password                                           	jp641	10.0.2.2	1516361586	0	0
lgol7l5vfv2qdtic2i3amugn83                                      	sysadmin	10.0.2.2	1516361636	0	1002
antevm5jinpes6anu71pve0ci2                                      	sysadmin	10.0.2.2	1516365293	0	1002
jr492ofqtcben7gcgdc7ofpg66                                      	sysadmin	10.0.2.2	1516365301	0	1002
ihgm6th0hjek1na57pdgk5rfq7                                      	sysadmin	10.0.2.2	1516365316	0	1002
31ijm8icf0hh2f8fepft7m19i2                                      	sysadmin	10.0.2.2	1516365387	0	1002
pa18sju96ck15ajq1hu8e07mk5                                      	sysadmin	10.0.2.2	1516365429	0	1002
5v2p2utt6u4c8ku63drvqmmsr1                                      	sysadmin	10.0.2.2	1516365669	0	1002
8rah5ep5652qat006mi2mb6ss3                                      	sysadmin	10.0.2.2	1516366728	0	1002
4vodgkvmhv6nmif11aiaohjjn5                                      	sysadmin	::1	1516392399	0	1002
gcsv8hrgvtkac6km2gts91h1c0                                      	sysadmin	::1	1516568033	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515491600	0	1002
bad login or password                                           	sysadmin	10.0.2.2	1515661010	0	0
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515661016	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1515761058	0	1002
7p2ufde9erdpaga88pe2090qt6                                      	sysadmin	10.0.2.2	1516087534	0	1002
bad login or password                                           	jp641	10.0.2.2	1516361586	0	0
lgol7l5vfv2qdtic2i3amugn83                                      	sysadmin	10.0.2.2	1516361636	0	1002
antevm5jinpes6anu71pve0ci2                                      	sysadmin	10.0.2.2	1516365293	0	1002
jr492ofqtcben7gcgdc7ofpg66                                      	sysadmin	10.0.2.2	1516365301	0	1002
ihgm6th0hjek1na57pdgk5rfq7                                      	sysadmin	10.0.2.2	1516365316	0	1002
31ijm8icf0hh2f8fepft7m19i2                                      	sysadmin	10.0.2.2	1516365387	0	1002
pa18sju96ck15ajq1hu8e07mk5                                      	sysadmin	10.0.2.2	1516365429	0	1002
5v2p2utt6u4c8ku63drvqmmsr1                                      	sysadmin	10.0.2.2	1516365669	0	1002
8rah5ep5652qat006mi2mb6ss3                                      	sysadmin	10.0.2.2	1516366728	0	1002
4vodgkvmhv6nmif11aiaohjjn5                                      	sysadmin	::1	1516392399	0	1002
jsnuv2gp6ak3uju9amp98qd0p1                                      	sysadmin	::1	1516568037	0	1002
mve68hrpo5ldppgq18p25g50b7                                      	sysadmin	10.0.2.2	1517583869	0	1002
mve68hrpo5ldppgq18p25g50b7                                      	sysadmin	10.0.2.2	1517584176	0	1002
\.


--
-- Data for Name: phpgw_account_delegates; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_account_delegates (delegate_id, account_id, owner_id, location_id, data, active_from, active_to, created_on, created_by) FROM stdin;
\.


--
-- Data for Name: phpgw_accounts; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_accounts (account_id, account_lid, account_pwd, account_firstname, account_lastname, account_permissions, account_groups, account_lastlogin, account_lastloginfrom, account_lastpwd_change, account_status, account_expires, account_type, person_id, account_quota) FROM stdin;
1000	default		Default	Group	\N	\N	\N	\N	\N	A	-1	g	1	-1
1001	admins		Admins	Group	\N	\N	\N	\N	\N	A	-1	g	2	-1
1003	rental_group		Rental	Group	\N	\N	\N	\N	\N	A	-1	g	4	-1
1004	rental_admin	{SSHA}/udhfiOIjhRBkEcjDPZUKUAaExISlg==	Rental	Administrator	\N	\N	\N	\N	\N	A	-1	u	5	0
1005	rental_internal	{SSHA}PFb8m6vrRSgx9ZGNaj5hUTgOXvHlNQ==	Rental	Internal	\N	\N	\N	\N	\N	A	-1	u	6	0
1006	rental_in	{SSHA}uQ0HS43zzaXL6CUM010uvb/hFqn9Ow==	Rental	In	\N	\N	\N	\N	\N	A	-1	u	7	0
1007	rental_out	{SSHA}u+ze+itaqkXHfb6bPdK6+v619SldwQ==	Rental	Out	\N	\N	\N	\N	\N	A	-1	u	8	0
1008	rental_manager	{SSHA}COrtFIX65F5oNfc2Fur8wwX1xGW4sQ==	Rental	Manager	\N	\N	\N	\N	\N	A	-1	u	9	0
1002	sysadmin	{SSHA}36GXgxF7YdWU4xUSsxHK8NlN41y2VA==	System	Administrator	\N	\N	1517584176	10.0.2.2	\N	A	-1	u	3	0
\.


--
-- Data for Name: phpgw_accounts_data; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_accounts_data (account_id, account_data) FROM stdin;
\.


--
-- Data for Name: phpgw_acl; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_acl (acl_account, acl_rights, acl_grantor, acl_type, location_id, modified_on, modified_by) FROM stdin;
1000	1	-1	0	5	1515488476	-1
1002	1	-1	0	7	1515488476	-1
1002	1	-1	0	3	1515488476	-1
1001	31	-1	0	23	1515760717	-1
1001	31	-1	0	31	1515760717	-1
1001	31	-1	0	35	1515760717	-1
1001	31	-1	0	65	1515760717	-1
1001	31	-1	0	52	1515760717	-1
1001	31	-1	0	101	1515760717	-1
1001	31	-1	0	20	1515760717	-1
1001	31	-1	0	69	1515760717	-1
1001	31	-1	0	44	1515760717	-1
1001	31	-1	0	37	1515760717	-1
1001	1	-1	0	10	1515760717	-1
1001	31	-1	0	56	1515760717	-1
1001	31	-1	0	40	1515760717	-1
1001	31	-1	0	53	1515760717	-1
1001	31	-1	0	19	1515760717	-1
1001	31	-1	0	57	1515760717	-1
1001	31	-1	0	51	1515760717	-1
1001	31	-1	0	92	1515760717	-1
1001	31	-1	0	66	1515760717	-1
1001	31	-1	0	30	1515760717	-1
1001	31	-1	0	50	1515760717	-1
1001	31	-1	0	33	1515760717	-1
1001	31	-1	0	73	1515760717	-1
1001	31	-1	0	95	1515760717	-1
1001	31	-1	0	14	1515760717	-1
1001	31	-1	0	46	1515760717	-1
1001	31	-1	0	99	1515760717	-1
1001	31	-1	0	48	1515760717	-1
1001	31	-1	0	17	1515760717	-1
1001	31	-1	0	28	1515760717	-1
1001	31	-1	0	83	1515760717	-1
1001	31	-1	0	36	1515760717	-1
1001	31	-1	0	94	1515760717	-1
1001	31	-1	0	15	1515760717	-1
1001	31	-1	0	61	1515760717	-1
1001	31	-1	0	86	1515760717	-1
1001	31	-1	0	96	1515760717	-1
1001	31	-1	0	13	1515760717	-1
1001	31	-1	0	49	1515760717	-1
1001	31	-1	0	22	1515760717	-1
1001	31	-1	0	63	1515760717	-1
1001	31	-1	0	87	1515760717	-1
1001	31	-1	0	24	1515760717	-1
1001	31	-1	0	91	1515760717	-1
1001	31	-1	0	54	1515760717	-1
1001	31	-1	0	98	1515760717	-1
1001	1	-1	0	89	1515760717	-1
1001	31	-1	0	100	1515760717	-1
1001	1	-1	0	104	1515760717	-1
1001	31	-1	0	47	1515760717	-1
1001	31	-1	0	103	1515760717	-1
1001	31	-1	0	42	1515760717	-1
1001	31	-1	0	26	1515760717	-1
1001	31	-1	0	11	1515760717	-1
1001	1	-1	0	62	1515760717	-1
1001	31	-1	0	90	1515760717	-1
1001	31	-1	0	80	1515760717	-1
1001	31	-1	0	18	1515760717	-1
1001	31	-1	0	59	1515760717	-1
1001	31	-1	0	78	1515760717	-1
1001	31	-1	0	39	1515760717	-1
1001	31	-1	0	16	1515760717	-1
1001	31	-1	0	85	1515760717	-1
1001	31	-1	0	34	1515760717	-1
1001	31	-1	0	43	1515760717	-1
1001	31	-1	0	82	1515760717	-1
1001	31	-1	0	81	1515760717	-1
1001	1	-1	0	76	1515760717	-1
1001	31	-1	0	25	1515760717	-1
1001	31	-1	0	32	1515760717	-1
1001	31	-1	0	12	1515760717	-1
1001	31	-1	0	58	1515760717	-1
1001	31	-1	0	79	1515760717	-1
1001	31	-1	0	41	1515760717	-1
1001	31	-1	0	75	1515760717	-1
1001	31	-1	0	102	1515760717	-1
1001	31	-1	0	71	1515760717	-1
1001	31	-1	0	29	1515760717	-1
1001	31	-1	0	93	1515760717	-1
1001	31	-1	0	105	1515760717	-1
1001	31	-1	0	21	1515760717	-1
1001	31	-1	0	72	1515760717	-1
1001	31	-1	0	97	1515760717	-1
1001	31	-1	0	38	1515760717	-1
1001	31	-1	0	60	1515760717	-1
1001	31	-1	0	74	1515760717	-1
1001	1	-1	0	3	1515760717	-1
1001	31	-1	0	70	1515760717	-1
1001	31	-1	0	64	1515760717	-1
1001	31	-1	0	45	1515760717	-1
1001	31	-1	0	27	1515760717	-1
1001	31	-1	0	55	1515760717	-1
1001	31	-1	0	68	1515760717	-1
1001	1	-1	0	67	1515760717	-1
1001	31	-1	0	84	1515760717	-1
1001	31	-1	0	88	1515760717	-1
1003	1	-1	0	25	1515760639	-1
1003	1	-1	0	32	1515760639	-1
1003	1	-1	0	81	1515760639	-1
1003	1	-1	0	34	1515760639	-1
1003	1	-1	0	43	1515760639	-1
1003	1	-1	0	82	1515760639	-1
1003	1	-1	0	85	1515760639	-1
1003	1	-1	0	10	1515760639	-1
1003	1	-1	0	79	1515760639	-1
1003	1	-1	0	58	1515760639	-1
1003	1	-1	0	12	1515760639	-1
1003	1	-1	0	18	1515760639	-1
1003	1	-1	0	80	1515760639	-1
1003	1	-1	0	26	1515760639	-1
1003	1	-1	0	42	1515760639	-1
1003	1	-1	0	16	1515760639	-1
1003	1	-1	0	39	1515760639	-1
1003	1	-1	0	59	1515760639	-1
1003	1	-1	0	78	1515760639	-1
1003	1	-1	0	54	1515760639	-1
1003	1	-1	0	47	1515760639	-1
1003	1	-1	0	61	1515760639	-1
1003	1	-1	0	86	1515760639	-1
1003	1	-1	0	24	1515760639	-1
1003	1	-1	0	87	1515760639	-1
1003	1	-1	0	22	1515760639	-1
1003	1	-1	0	13	1515760639	-1
1003	1	-1	0	49	1515760639	-1
1003	1	-1	0	27	1515760639	-1
1003	1	-1	0	48	1515760639	-1
1003	1	-1	0	55	1515760639	-1
1003	1	-1	0	46	1515760639	-1
1003	1	-1	0	14	1515760639	-1
1003	1	-1	0	45	1515760639	-1
1003	1	-1	0	88	1515760639	-1
1003	1	-1	0	15	1515760639	-1
1003	1	-1	0	84	1515760639	-1
1003	1	-1	0	36	1515760639	-1
1003	1	-1	0	17	1515760639	-1
1003	1	-1	0	28	1515760639	-1
1003	1	-1	0	83	1515760639	-1
1003	1	-1	0	30	1515760639	-1
1003	1	-1	0	38	1515760639	-1
1003	1	-1	0	33	1515760639	-1
1003	1	-1	0	50	1515760639	-1
1003	1	-1	0	60	1515760639	-1
1003	1	-1	0	53	1515760639	-1
1003	1	-1	0	40	1515760639	-1
1003	1	-1	0	56	1515760639	-1
1003	1	-1	0	51	1515760639	-1
1003	1	-1	0	21	1515760639	-1
1003	1	-1	0	57	1515760639	-1
1003	1	-1	0	29	1515760639	-1
1003	1	-1	0	19	1515760639	-1
1003	1	-1	0	20	1515760639	-1
1003	1	-1	0	52	1515760639	-1
1003	1	-1	0	76	1515760639	-1
1003	1	-1	0	31	1515760639	-1
1003	1	-1	0	35	1515760639	-1
1003	1	-1	0	23	1515760639	-1
1003	1	-1	0	41	1515760639	-1
1003	1	-1	0	7	1515760639	-1
1003	1	-1	0	37	1515760639	-1
1003	1	-1	0	5	1515760639	-1
1003	1	-1	0	44	1515760639	-1
1004	31	-1	0	87	1515760639	-1
1004	1	-1	0	3	1515760639	-1
1004	31	-1	0	78	1515760639	-1
1004	31	-1	0	80	1515760639	-1
1004	31	-1	0	81	1515760639	-1
1004	31	-1	0	85	1515760639	-1
1004	31	-1	0	82	1515760639	-1
1004	31	-1	0	86	1515760639	-1
1004	31	-1	0	84	1515760639	-1
1004	31	-1	0	79	1515760639	-1
1004	31	-1	0	88	1515760639	-1
1004	31	-1	0	83	1515760639	-1
1005	15	-1	0	86	1515760639	-1
1006	15	-1	0	87	1515760639	-1
1007	15	-1	0	88	1515760639	-1
1000	1	-1	0	5	1515488476	-1
1002	1	-1	0	7	1515488476	-1
1002	1	-1	0	3	1515488476	-1
1001	31	-1	0	23	1515760717	-1
1001	31	-1	0	31	1515760717	-1
1001	31	-1	0	35	1515760717	-1
1001	31	-1	0	65	1515760717	-1
1001	31	-1	0	52	1515760717	-1
1001	31	-1	0	101	1515760717	-1
1001	31	-1	0	20	1515760717	-1
1001	31	-1	0	69	1515760717	-1
1001	31	-1	0	44	1515760717	-1
1001	31	-1	0	37	1515760717	-1
1001	1	-1	0	10	1515760717	-1
1001	31	-1	0	56	1515760717	-1
1001	31	-1	0	40	1515760717	-1
1001	31	-1	0	53	1515760717	-1
1001	31	-1	0	19	1515760717	-1
1001	31	-1	0	57	1515760717	-1
1001	31	-1	0	51	1515760717	-1
1001	31	-1	0	92	1515760717	-1
1001	31	-1	0	66	1515760717	-1
1001	31	-1	0	30	1515760717	-1
1001	31	-1	0	50	1515760717	-1
1001	31	-1	0	33	1515760717	-1
1001	31	-1	0	73	1515760717	-1
1001	31	-1	0	95	1515760717	-1
1001	31	-1	0	14	1515760717	-1
1001	31	-1	0	46	1515760717	-1
1001	31	-1	0	99	1515760717	-1
1001	31	-1	0	48	1515760717	-1
1001	31	-1	0	17	1515760717	-1
1001	31	-1	0	28	1515760717	-1
1001	31	-1	0	83	1515760717	-1
1001	31	-1	0	36	1515760717	-1
1001	31	-1	0	94	1515760717	-1
1001	31	-1	0	15	1515760717	-1
1001	31	-1	0	61	1515760717	-1
1001	31	-1	0	86	1515760717	-1
1001	31	-1	0	96	1515760717	-1
1001	31	-1	0	13	1515760717	-1
1001	31	-1	0	49	1515760717	-1
1001	31	-1	0	22	1515760717	-1
1001	31	-1	0	63	1515760717	-1
1001	31	-1	0	87	1515760717	-1
1001	31	-1	0	24	1515760717	-1
1001	31	-1	0	91	1515760717	-1
1001	31	-1	0	54	1515760717	-1
1001	31	-1	0	98	1515760717	-1
1001	1	-1	0	89	1515760717	-1
1001	31	-1	0	100	1515760717	-1
1001	1	-1	0	104	1515760717	-1
1001	31	-1	0	47	1515760717	-1
1001	31	-1	0	103	1515760717	-1
1001	31	-1	0	42	1515760717	-1
1001	31	-1	0	26	1515760717	-1
1001	31	-1	0	11	1515760717	-1
1001	1	-1	0	62	1515760717	-1
1001	31	-1	0	90	1515760717	-1
1001	31	-1	0	80	1515760717	-1
1001	31	-1	0	18	1515760717	-1
1001	31	-1	0	59	1515760717	-1
1001	31	-1	0	78	1515760717	-1
1001	31	-1	0	39	1515760717	-1
1001	31	-1	0	16	1515760717	-1
1001	31	-1	0	85	1515760717	-1
1001	31	-1	0	34	1515760717	-1
1001	31	-1	0	43	1515760717	-1
1001	31	-1	0	82	1515760717	-1
1001	31	-1	0	81	1515760717	-1
1001	1	-1	0	76	1515760717	-1
1001	31	-1	0	25	1515760717	-1
1001	31	-1	0	32	1515760717	-1
1001	31	-1	0	12	1515760717	-1
1001	31	-1	0	58	1515760717	-1
1001	31	-1	0	79	1515760717	-1
1001	31	-1	0	41	1515760717	-1
1001	31	-1	0	75	1515760717	-1
1001	31	-1	0	102	1515760717	-1
1001	31	-1	0	71	1515760717	-1
1001	31	-1	0	29	1515760717	-1
1001	31	-1	0	93	1515760717	-1
1001	31	-1	0	105	1515760717	-1
1001	31	-1	0	21	1515760717	-1
1001	31	-1	0	72	1515760717	-1
1001	31	-1	0	97	1515760717	-1
1001	31	-1	0	38	1515760717	-1
1001	31	-1	0	60	1515760717	-1
1001	31	-1	0	74	1515760717	-1
1001	1	-1	0	3	1515760717	-1
1001	31	-1	0	70	1515760717	-1
1001	31	-1	0	64	1515760717	-1
1001	31	-1	0	45	1515760717	-1
1001	31	-1	0	27	1515760717	-1
1001	31	-1	0	55	1515760717	-1
1001	31	-1	0	68	1515760717	-1
1001	1	-1	0	67	1515760717	-1
1001	31	-1	0	84	1515760717	-1
1001	31	-1	0	88	1515760717	-1
1003	1	-1	0	25	1515760639	-1
1003	1	-1	0	32	1515760639	-1
1003	1	-1	0	81	1515760639	-1
1003	1	-1	0	34	1515760639	-1
1003	1	-1	0	43	1515760639	-1
1003	1	-1	0	82	1515760639	-1
1003	1	-1	0	85	1515760639	-1
1003	1	-1	0	10	1515760639	-1
1003	1	-1	0	79	1515760639	-1
1003	1	-1	0	58	1515760639	-1
1003	1	-1	0	12	1515760639	-1
1003	1	-1	0	18	1515760639	-1
1003	1	-1	0	80	1515760639	-1
1003	1	-1	0	26	1515760639	-1
1003	1	-1	0	42	1515760639	-1
1003	1	-1	0	16	1515760639	-1
1003	1	-1	0	39	1515760639	-1
1003	1	-1	0	59	1515760639	-1
1003	1	-1	0	78	1515760639	-1
1003	1	-1	0	54	1515760639	-1
1003	1	-1	0	47	1515760639	-1
1003	1	-1	0	61	1515760639	-1
1003	1	-1	0	86	1515760639	-1
1003	1	-1	0	24	1515760639	-1
1003	1	-1	0	87	1515760639	-1
1003	1	-1	0	22	1515760639	-1
1003	1	-1	0	13	1515760639	-1
1003	1	-1	0	49	1515760639	-1
1003	1	-1	0	27	1515760639	-1
1003	1	-1	0	48	1515760639	-1
1003	1	-1	0	55	1515760639	-1
1003	1	-1	0	46	1515760639	-1
1003	1	-1	0	14	1515760639	-1
1003	1	-1	0	45	1515760639	-1
1003	1	-1	0	88	1515760639	-1
1003	1	-1	0	15	1515760639	-1
1003	1	-1	0	84	1515760639	-1
1003	1	-1	0	36	1515760639	-1
1003	1	-1	0	17	1515760639	-1
1003	1	-1	0	28	1515760639	-1
1003	1	-1	0	83	1515760639	-1
1003	1	-1	0	30	1515760639	-1
1003	1	-1	0	38	1515760639	-1
1003	1	-1	0	33	1515760639	-1
1003	1	-1	0	50	1515760639	-1
1003	1	-1	0	60	1515760639	-1
1003	1	-1	0	53	1515760639	-1
1003	1	-1	0	40	1515760639	-1
1003	1	-1	0	56	1515760639	-1
1003	1	-1	0	51	1515760639	-1
1003	1	-1	0	21	1515760639	-1
1003	1	-1	0	57	1515760639	-1
1003	1	-1	0	29	1515760639	-1
1003	1	-1	0	19	1515760639	-1
1003	1	-1	0	20	1515760639	-1
1003	1	-1	0	52	1515760639	-1
1003	1	-1	0	76	1515760639	-1
1003	1	-1	0	31	1515760639	-1
1003	1	-1	0	35	1515760639	-1
1003	1	-1	0	23	1515760639	-1
1003	1	-1	0	41	1515760639	-1
1003	1	-1	0	7	1515760639	-1
1003	1	-1	0	37	1515760639	-1
1003	1	-1	0	5	1515760639	-1
1003	1	-1	0	44	1515760639	-1
1004	31	-1	0	87	1515760639	-1
1004	1	-1	0	3	1515760639	-1
1004	31	-1	0	78	1515760639	-1
1004	31	-1	0	80	1515760639	-1
1004	31	-1	0	81	1515760639	-1
1004	31	-1	0	85	1515760639	-1
1004	31	-1	0	82	1515760639	-1
1004	31	-1	0	86	1515760639	-1
1004	31	-1	0	84	1515760639	-1
1004	31	-1	0	79	1515760639	-1
1004	31	-1	0	88	1515760639	-1
1004	31	-1	0	83	1515760639	-1
1005	15	-1	0	86	1515760639	-1
1006	15	-1	0	87	1515760639	-1
1007	15	-1	0	88	1515760639	-1
1000	1	-1	0	5	1515488476	-1
1002	1	-1	0	7	1515488476	-1
1002	1	-1	0	3	1515488476	-1
1001	31	-1	0	23	1515760717	-1
1001	31	-1	0	31	1515760717	-1
1001	31	-1	0	35	1515760717	-1
1001	31	-1	0	65	1515760717	-1
1001	31	-1	0	52	1515760717	-1
1001	31	-1	0	101	1515760717	-1
1001	31	-1	0	20	1515760717	-1
1001	31	-1	0	69	1515760717	-1
1001	31	-1	0	44	1515760717	-1
1001	31	-1	0	37	1515760717	-1
1001	1	-1	0	10	1515760717	-1
1001	31	-1	0	56	1515760717	-1
1001	31	-1	0	40	1515760717	-1
1001	31	-1	0	53	1515760717	-1
1001	31	-1	0	19	1515760717	-1
1001	31	-1	0	57	1515760717	-1
1001	31	-1	0	51	1515760717	-1
1001	31	-1	0	92	1515760717	-1
1001	31	-1	0	66	1515760717	-1
1001	31	-1	0	30	1515760717	-1
1001	31	-1	0	50	1515760717	-1
1001	31	-1	0	33	1515760717	-1
1001	31	-1	0	73	1515760717	-1
1001	31	-1	0	95	1515760717	-1
1001	31	-1	0	14	1515760717	-1
1001	31	-1	0	46	1515760717	-1
1001	31	-1	0	99	1515760717	-1
1001	31	-1	0	48	1515760717	-1
1001	31	-1	0	17	1515760717	-1
1001	31	-1	0	28	1515760717	-1
1001	31	-1	0	83	1515760717	-1
1001	31	-1	0	36	1515760717	-1
1001	31	-1	0	94	1515760717	-1
1001	31	-1	0	15	1515760717	-1
1001	31	-1	0	61	1515760717	-1
1001	31	-1	0	86	1515760717	-1
1001	31	-1	0	96	1515760717	-1
1001	31	-1	0	13	1515760717	-1
1001	31	-1	0	49	1515760717	-1
1001	31	-1	0	22	1515760717	-1
1001	31	-1	0	63	1515760717	-1
1001	31	-1	0	87	1515760717	-1
1001	31	-1	0	24	1515760717	-1
1001	31	-1	0	91	1515760717	-1
1001	31	-1	0	54	1515760717	-1
1001	31	-1	0	98	1515760717	-1
1001	1	-1	0	89	1515760717	-1
1001	31	-1	0	100	1515760717	-1
1001	1	-1	0	104	1515760717	-1
1001	31	-1	0	47	1515760717	-1
1001	31	-1	0	103	1515760717	-1
1001	31	-1	0	42	1515760717	-1
1001	31	-1	0	26	1515760717	-1
1001	31	-1	0	11	1515760717	-1
1001	1	-1	0	62	1515760717	-1
1001	31	-1	0	90	1515760717	-1
1001	31	-1	0	80	1515760717	-1
1001	31	-1	0	18	1515760717	-1
1001	31	-1	0	59	1515760717	-1
1001	31	-1	0	78	1515760717	-1
1001	31	-1	0	39	1515760717	-1
1001	31	-1	0	16	1515760717	-1
1001	31	-1	0	85	1515760717	-1
1001	31	-1	0	34	1515760717	-1
1001	31	-1	0	43	1515760717	-1
1001	31	-1	0	82	1515760717	-1
1001	31	-1	0	81	1515760717	-1
1001	1	-1	0	76	1515760717	-1
1001	31	-1	0	25	1515760717	-1
1001	31	-1	0	32	1515760717	-1
1001	31	-1	0	12	1515760717	-1
1001	31	-1	0	58	1515760717	-1
1001	31	-1	0	79	1515760717	-1
1001	31	-1	0	41	1515760717	-1
1001	31	-1	0	75	1515760717	-1
1001	31	-1	0	102	1515760717	-1
1001	31	-1	0	71	1515760717	-1
1001	31	-1	0	29	1515760717	-1
1001	31	-1	0	93	1515760717	-1
1001	31	-1	0	105	1515760717	-1
1001	31	-1	0	21	1515760717	-1
1001	31	-1	0	72	1515760717	-1
1001	31	-1	0	97	1515760717	-1
1001	31	-1	0	38	1515760717	-1
1001	31	-1	0	60	1515760717	-1
1001	31	-1	0	74	1515760717	-1
1001	1	-1	0	3	1515760717	-1
1001	31	-1	0	70	1515760717	-1
1001	31	-1	0	64	1515760717	-1
1001	31	-1	0	45	1515760717	-1
1001	31	-1	0	27	1515760717	-1
1001	31	-1	0	55	1515760717	-1
1001	31	-1	0	68	1515760717	-1
1001	1	-1	0	67	1515760717	-1
1001	31	-1	0	84	1515760717	-1
1001	31	-1	0	88	1515760717	-1
1003	1	-1	0	25	1515760639	-1
1003	1	-1	0	32	1515760639	-1
1003	1	-1	0	81	1515760639	-1
1003	1	-1	0	34	1515760639	-1
1003	1	-1	0	43	1515760639	-1
1003	1	-1	0	82	1515760639	-1
1003	1	-1	0	85	1515760639	-1
1003	1	-1	0	10	1515760639	-1
1003	1	-1	0	79	1515760639	-1
1003	1	-1	0	58	1515760639	-1
1003	1	-1	0	12	1515760639	-1
1003	1	-1	0	18	1515760639	-1
1003	1	-1	0	80	1515760639	-1
1003	1	-1	0	26	1515760639	-1
1003	1	-1	0	42	1515760639	-1
1003	1	-1	0	16	1515760639	-1
1003	1	-1	0	39	1515760639	-1
1003	1	-1	0	59	1515760639	-1
1003	1	-1	0	78	1515760639	-1
1003	1	-1	0	54	1515760639	-1
1003	1	-1	0	47	1515760639	-1
1003	1	-1	0	61	1515760639	-1
1003	1	-1	0	86	1515760639	-1
1003	1	-1	0	24	1515760639	-1
1003	1	-1	0	87	1515760639	-1
1003	1	-1	0	22	1515760639	-1
1003	1	-1	0	13	1515760639	-1
1003	1	-1	0	49	1515760639	-1
1003	1	-1	0	27	1515760639	-1
1003	1	-1	0	48	1515760639	-1
1003	1	-1	0	55	1515760639	-1
1003	1	-1	0	46	1515760639	-1
1003	1	-1	0	14	1515760639	-1
1003	1	-1	0	45	1515760639	-1
1003	1	-1	0	88	1515760639	-1
1003	1	-1	0	15	1515760639	-1
1003	1	-1	0	84	1515760639	-1
1003	1	-1	0	36	1515760639	-1
1003	1	-1	0	17	1515760639	-1
1003	1	-1	0	28	1515760639	-1
1003	1	-1	0	83	1515760639	-1
1003	1	-1	0	30	1515760639	-1
1003	1	-1	0	38	1515760639	-1
1003	1	-1	0	33	1515760639	-1
1003	1	-1	0	50	1515760639	-1
1003	1	-1	0	60	1515760639	-1
1003	1	-1	0	53	1515760639	-1
1003	1	-1	0	40	1515760639	-1
1003	1	-1	0	56	1515760639	-1
1003	1	-1	0	51	1515760639	-1
1003	1	-1	0	21	1515760639	-1
1003	1	-1	0	57	1515760639	-1
1003	1	-1	0	29	1515760639	-1
1003	1	-1	0	19	1515760639	-1
1003	1	-1	0	20	1515760639	-1
1003	1	-1	0	52	1515760639	-1
1003	1	-1	0	76	1515760639	-1
1003	1	-1	0	31	1515760639	-1
1003	1	-1	0	35	1515760639	-1
1003	1	-1	0	23	1515760639	-1
1003	1	-1	0	41	1515760639	-1
1003	1	-1	0	7	1515760639	-1
1003	1	-1	0	37	1515760639	-1
1003	1	-1	0	5	1515760639	-1
1003	1	-1	0	44	1515760639	-1
1004	31	-1	0	87	1515760639	-1
1004	1	-1	0	3	1515760639	-1
1004	31	-1	0	78	1515760639	-1
1004	31	-1	0	80	1515760639	-1
1004	31	-1	0	81	1515760639	-1
1004	31	-1	0	85	1515760639	-1
1004	31	-1	0	82	1515760639	-1
1004	31	-1	0	86	1515760639	-1
1004	31	-1	0	84	1515760639	-1
1004	31	-1	0	79	1515760639	-1
1004	31	-1	0	88	1515760639	-1
1004	31	-1	0	83	1515760639	-1
1005	15	-1	0	86	1515760639	-1
1006	15	-1	0	87	1515760639	-1
1007	15	-1	0	88	1515760639	-1
1000	1	-1	0	5	1515488476	-1
1002	1	-1	0	7	1515488476	-1
1002	1	-1	0	3	1515488476	-1
1001	31	-1	0	23	1515760717	-1
1001	31	-1	0	31	1515760717	-1
1001	31	-1	0	35	1515760717	-1
1001	31	-1	0	65	1515760717	-1
1001	31	-1	0	52	1515760717	-1
1001	31	-1	0	101	1515760717	-1
1001	31	-1	0	20	1515760717	-1
1001	31	-1	0	69	1515760717	-1
1001	31	-1	0	44	1515760717	-1
1001	31	-1	0	37	1515760717	-1
1001	1	-1	0	10	1515760717	-1
1001	31	-1	0	56	1515760717	-1
1001	31	-1	0	40	1515760717	-1
1001	31	-1	0	53	1515760717	-1
1001	31	-1	0	19	1515760717	-1
1001	31	-1	0	57	1515760717	-1
1001	31	-1	0	51	1515760717	-1
1001	31	-1	0	92	1515760717	-1
1001	31	-1	0	66	1515760717	-1
1001	31	-1	0	30	1515760717	-1
1001	31	-1	0	50	1515760717	-1
1001	31	-1	0	33	1515760717	-1
1001	31	-1	0	73	1515760717	-1
1001	31	-1	0	95	1515760717	-1
1001	31	-1	0	14	1515760717	-1
1001	31	-1	0	46	1515760717	-1
1001	31	-1	0	99	1515760717	-1
1001	31	-1	0	48	1515760717	-1
1001	31	-1	0	17	1515760717	-1
1001	31	-1	0	28	1515760717	-1
1001	31	-1	0	83	1515760717	-1
1001	31	-1	0	36	1515760717	-1
1001	31	-1	0	94	1515760717	-1
1001	31	-1	0	15	1515760717	-1
1001	31	-1	0	61	1515760717	-1
1001	31	-1	0	86	1515760717	-1
1001	31	-1	0	96	1515760717	-1
1001	31	-1	0	13	1515760717	-1
1001	31	-1	0	49	1515760717	-1
1001	31	-1	0	22	1515760717	-1
1001	31	-1	0	63	1515760717	-1
1001	31	-1	0	87	1515760717	-1
1001	31	-1	0	24	1515760717	-1
1001	31	-1	0	91	1515760717	-1
1001	31	-1	0	54	1515760717	-1
1001	31	-1	0	98	1515760717	-1
1001	1	-1	0	89	1515760717	-1
1001	31	-1	0	100	1515760717	-1
1001	1	-1	0	104	1515760717	-1
1001	31	-1	0	47	1515760717	-1
1001	31	-1	0	103	1515760717	-1
1001	31	-1	0	42	1515760717	-1
1001	31	-1	0	26	1515760717	-1
1001	31	-1	0	11	1515760717	-1
1001	1	-1	0	62	1515760717	-1
1001	31	-1	0	90	1515760717	-1
1001	31	-1	0	80	1515760717	-1
1001	31	-1	0	18	1515760717	-1
1001	31	-1	0	59	1515760717	-1
1001	31	-1	0	78	1515760717	-1
1001	31	-1	0	39	1515760717	-1
1001	31	-1	0	16	1515760717	-1
1001	31	-1	0	85	1515760717	-1
1001	31	-1	0	34	1515760717	-1
1001	31	-1	0	43	1515760717	-1
1001	31	-1	0	82	1515760717	-1
1001	31	-1	0	81	1515760717	-1
1001	1	-1	0	76	1515760717	-1
1001	31	-1	0	25	1515760717	-1
1001	31	-1	0	32	1515760717	-1
1001	31	-1	0	12	1515760717	-1
1001	31	-1	0	58	1515760717	-1
1001	31	-1	0	79	1515760717	-1
1001	31	-1	0	41	1515760717	-1
1001	31	-1	0	75	1515760717	-1
1001	31	-1	0	102	1515760717	-1
1001	31	-1	0	71	1515760717	-1
1001	31	-1	0	29	1515760717	-1
1001	31	-1	0	93	1515760717	-1
1001	31	-1	0	105	1515760717	-1
1001	31	-1	0	21	1515760717	-1
1001	31	-1	0	72	1515760717	-1
1001	31	-1	0	97	1515760717	-1
1001	31	-1	0	38	1515760717	-1
1001	31	-1	0	60	1515760717	-1
1001	31	-1	0	74	1515760717	-1
1001	1	-1	0	3	1515760717	-1
1001	31	-1	0	70	1515760717	-1
1001	31	-1	0	64	1515760717	-1
1001	31	-1	0	45	1515760717	-1
1001	31	-1	0	27	1515760717	-1
1001	31	-1	0	55	1515760717	-1
1001	31	-1	0	68	1515760717	-1
1001	1	-1	0	67	1515760717	-1
1001	31	-1	0	84	1515760717	-1
1001	31	-1	0	88	1515760717	-1
1003	1	-1	0	25	1515760639	-1
1003	1	-1	0	32	1515760639	-1
1003	1	-1	0	81	1515760639	-1
1003	1	-1	0	34	1515760639	-1
1003	1	-1	0	43	1515760639	-1
1003	1	-1	0	82	1515760639	-1
1003	1	-1	0	85	1515760639	-1
1003	1	-1	0	10	1515760639	-1
1003	1	-1	0	79	1515760639	-1
1003	1	-1	0	58	1515760639	-1
1003	1	-1	0	12	1515760639	-1
1003	1	-1	0	18	1515760639	-1
1003	1	-1	0	80	1515760639	-1
1003	1	-1	0	26	1515760639	-1
1003	1	-1	0	42	1515760639	-1
1003	1	-1	0	16	1515760639	-1
1003	1	-1	0	39	1515760639	-1
1003	1	-1	0	59	1515760639	-1
1003	1	-1	0	78	1515760639	-1
1003	1	-1	0	54	1515760639	-1
1003	1	-1	0	47	1515760639	-1
1003	1	-1	0	61	1515760639	-1
1003	1	-1	0	86	1515760639	-1
1003	1	-1	0	24	1515760639	-1
1003	1	-1	0	87	1515760639	-1
1003	1	-1	0	22	1515760639	-1
1003	1	-1	0	13	1515760639	-1
1003	1	-1	0	49	1515760639	-1
1003	1	-1	0	27	1515760639	-1
1003	1	-1	0	48	1515760639	-1
1003	1	-1	0	55	1515760639	-1
1003	1	-1	0	46	1515760639	-1
1003	1	-1	0	14	1515760639	-1
1003	1	-1	0	45	1515760639	-1
1003	1	-1	0	88	1515760639	-1
1003	1	-1	0	15	1515760639	-1
1003	1	-1	0	84	1515760639	-1
1003	1	-1	0	36	1515760639	-1
1003	1	-1	0	17	1515760639	-1
1003	1	-1	0	28	1515760639	-1
1003	1	-1	0	83	1515760639	-1
1003	1	-1	0	30	1515760639	-1
1003	1	-1	0	38	1515760639	-1
1003	1	-1	0	33	1515760639	-1
1003	1	-1	0	50	1515760639	-1
1003	1	-1	0	60	1515760639	-1
1003	1	-1	0	53	1515760639	-1
1003	1	-1	0	40	1515760639	-1
1003	1	-1	0	56	1515760639	-1
1003	1	-1	0	51	1515760639	-1
1003	1	-1	0	21	1515760639	-1
1003	1	-1	0	57	1515760639	-1
1003	1	-1	0	29	1515760639	-1
1003	1	-1	0	19	1515760639	-1
1003	1	-1	0	20	1515760639	-1
1003	1	-1	0	52	1515760639	-1
1003	1	-1	0	76	1515760639	-1
1003	1	-1	0	31	1515760639	-1
1003	1	-1	0	35	1515760639	-1
1003	1	-1	0	23	1515760639	-1
1003	1	-1	0	41	1515760639	-1
1003	1	-1	0	7	1515760639	-1
1003	1	-1	0	37	1515760639	-1
1003	1	-1	0	5	1515760639	-1
1003	1	-1	0	44	1515760639	-1
1004	31	-1	0	87	1515760639	-1
1004	1	-1	0	3	1515760639	-1
1004	31	-1	0	78	1515760639	-1
1004	31	-1	0	80	1515760639	-1
1004	31	-1	0	81	1515760639	-1
1004	31	-1	0	85	1515760639	-1
1004	31	-1	0	82	1515760639	-1
1004	31	-1	0	86	1515760639	-1
1004	31	-1	0	84	1515760639	-1
1004	31	-1	0	79	1515760639	-1
1004	31	-1	0	88	1515760639	-1
1004	31	-1	0	83	1515760639	-1
1005	15	-1	0	86	1515760639	-1
1006	15	-1	0	87	1515760639	-1
1007	15	-1	0	88	1515760639	-1
1000	1	-1	0	5	1515488476	-1
1002	1	-1	0	7	1515488476	-1
1002	1	-1	0	3	1515488476	-1
1001	31	-1	0	23	1515760717	-1
1001	31	-1	0	31	1515760717	-1
1001	31	-1	0	35	1515760717	-1
1001	31	-1	0	65	1515760717	-1
1001	31	-1	0	52	1515760717	-1
1001	31	-1	0	101	1515760717	-1
1001	31	-1	0	20	1515760717	-1
1001	31	-1	0	69	1515760717	-1
1001	31	-1	0	44	1515760717	-1
1001	31	-1	0	37	1515760717	-1
1001	1	-1	0	10	1515760717	-1
1001	31	-1	0	56	1515760717	-1
1001	31	-1	0	40	1515760717	-1
1001	31	-1	0	53	1515760717	-1
1001	31	-1	0	19	1515760717	-1
1001	31	-1	0	57	1515760717	-1
1001	31	-1	0	51	1515760717	-1
1001	31	-1	0	92	1515760717	-1
1001	31	-1	0	66	1515760717	-1
1001	31	-1	0	30	1515760717	-1
1001	31	-1	0	50	1515760717	-1
1001	31	-1	0	33	1515760717	-1
1001	31	-1	0	73	1515760717	-1
1001	31	-1	0	95	1515760717	-1
1001	31	-1	0	14	1515760717	-1
1001	31	-1	0	46	1515760717	-1
1001	31	-1	0	99	1515760717	-1
1001	31	-1	0	48	1515760717	-1
1001	31	-1	0	17	1515760717	-1
1001	31	-1	0	28	1515760717	-1
1001	31	-1	0	83	1515760717	-1
1001	31	-1	0	36	1515760717	-1
1001	31	-1	0	94	1515760717	-1
1001	31	-1	0	15	1515760717	-1
1001	31	-1	0	61	1515760717	-1
1001	31	-1	0	86	1515760717	-1
1001	31	-1	0	96	1515760717	-1
1001	31	-1	0	13	1515760717	-1
1001	31	-1	0	49	1515760717	-1
1001	31	-1	0	22	1515760717	-1
1001	31	-1	0	63	1515760717	-1
1001	31	-1	0	87	1515760717	-1
1001	31	-1	0	24	1515760717	-1
1001	31	-1	0	91	1515760717	-1
1001	31	-1	0	54	1515760717	-1
1001	31	-1	0	98	1515760717	-1
1001	1	-1	0	89	1515760717	-1
1001	31	-1	0	100	1515760717	-1
1001	1	-1	0	104	1515760717	-1
1001	31	-1	0	47	1515760717	-1
1001	31	-1	0	103	1515760717	-1
1001	31	-1	0	42	1515760717	-1
1001	31	-1	0	26	1515760717	-1
1001	31	-1	0	11	1515760717	-1
1001	1	-1	0	62	1515760717	-1
1001	31	-1	0	90	1515760717	-1
1001	31	-1	0	80	1515760717	-1
1001	31	-1	0	18	1515760717	-1
1001	31	-1	0	59	1515760717	-1
1001	31	-1	0	78	1515760717	-1
1001	31	-1	0	39	1515760717	-1
1001	31	-1	0	16	1515760717	-1
1001	31	-1	0	85	1515760717	-1
1001	31	-1	0	34	1515760717	-1
1001	31	-1	0	43	1515760717	-1
1001	31	-1	0	82	1515760717	-1
1001	31	-1	0	81	1515760717	-1
1001	1	-1	0	76	1515760717	-1
1001	31	-1	0	25	1515760717	-1
1001	31	-1	0	32	1515760717	-1
1001	31	-1	0	12	1515760717	-1
1001	31	-1	0	58	1515760717	-1
1001	31	-1	0	79	1515760717	-1
1001	31	-1	0	41	1515760717	-1
1001	31	-1	0	75	1515760717	-1
1001	31	-1	0	102	1515760717	-1
1001	31	-1	0	71	1515760717	-1
1001	31	-1	0	29	1515760717	-1
1001	31	-1	0	93	1515760717	-1
1001	31	-1	0	105	1515760717	-1
1001	31	-1	0	21	1515760717	-1
1001	31	-1	0	72	1515760717	-1
1001	31	-1	0	97	1515760717	-1
1001	31	-1	0	38	1515760717	-1
1001	31	-1	0	60	1515760717	-1
1001	31	-1	0	74	1515760717	-1
1001	1	-1	0	3	1515760717	-1
1001	31	-1	0	70	1515760717	-1
1001	31	-1	0	64	1515760717	-1
1001	31	-1	0	45	1515760717	-1
1001	31	-1	0	27	1515760717	-1
1001	31	-1	0	55	1515760717	-1
1001	31	-1	0	68	1515760717	-1
1001	1	-1	0	67	1515760717	-1
1001	31	-1	0	84	1515760717	-1
1001	31	-1	0	88	1515760717	-1
1003	1	-1	0	25	1515760639	-1
1003	1	-1	0	32	1515760639	-1
1003	1	-1	0	81	1515760639	-1
1003	1	-1	0	34	1515760639	-1
1003	1	-1	0	43	1515760639	-1
1003	1	-1	0	82	1515760639	-1
1003	1	-1	0	85	1515760639	-1
1003	1	-1	0	10	1515760639	-1
1003	1	-1	0	79	1515760639	-1
1003	1	-1	0	58	1515760639	-1
1003	1	-1	0	12	1515760639	-1
1003	1	-1	0	18	1515760639	-1
1003	1	-1	0	80	1515760639	-1
1003	1	-1	0	26	1515760639	-1
1003	1	-1	0	42	1515760639	-1
1003	1	-1	0	16	1515760639	-1
1003	1	-1	0	39	1515760639	-1
1003	1	-1	0	59	1515760639	-1
1003	1	-1	0	78	1515760639	-1
1003	1	-1	0	54	1515760639	-1
1003	1	-1	0	47	1515760639	-1
1003	1	-1	0	61	1515760639	-1
1003	1	-1	0	86	1515760639	-1
1003	1	-1	0	24	1515760639	-1
1003	1	-1	0	87	1515760639	-1
1003	1	-1	0	22	1515760639	-1
1003	1	-1	0	13	1515760639	-1
1003	1	-1	0	49	1515760639	-1
1003	1	-1	0	27	1515760639	-1
1003	1	-1	0	48	1515760639	-1
1003	1	-1	0	55	1515760639	-1
1003	1	-1	0	46	1515760639	-1
1003	1	-1	0	14	1515760639	-1
1003	1	-1	0	45	1515760639	-1
1003	1	-1	0	88	1515760639	-1
1003	1	-1	0	15	1515760639	-1
1003	1	-1	0	84	1515760639	-1
1003	1	-1	0	36	1515760639	-1
1003	1	-1	0	17	1515760639	-1
1003	1	-1	0	28	1515760639	-1
1003	1	-1	0	83	1515760639	-1
1003	1	-1	0	30	1515760639	-1
1003	1	-1	0	38	1515760639	-1
1003	1	-1	0	33	1515760639	-1
1003	1	-1	0	50	1515760639	-1
1003	1	-1	0	60	1515760639	-1
1003	1	-1	0	53	1515760639	-1
1003	1	-1	0	40	1515760639	-1
1003	1	-1	0	56	1515760639	-1
1003	1	-1	0	51	1515760639	-1
1003	1	-1	0	21	1515760639	-1
1003	1	-1	0	57	1515760639	-1
1003	1	-1	0	29	1515760639	-1
1003	1	-1	0	19	1515760639	-1
1003	1	-1	0	20	1515760639	-1
1003	1	-1	0	52	1515760639	-1
1003	1	-1	0	76	1515760639	-1
1003	1	-1	0	31	1515760639	-1
1003	1	-1	0	35	1515760639	-1
1003	1	-1	0	23	1515760639	-1
1003	1	-1	0	41	1515760639	-1
1003	1	-1	0	7	1515760639	-1
1003	1	-1	0	37	1515760639	-1
1003	1	-1	0	5	1515760639	-1
1003	1	-1	0	44	1515760639	-1
1004	31	-1	0	87	1515760639	-1
1004	1	-1	0	3	1515760639	-1
1004	31	-1	0	78	1515760639	-1
1004	31	-1	0	80	1515760639	-1
1004	31	-1	0	81	1515760639	-1
1004	31	-1	0	85	1515760639	-1
1004	31	-1	0	82	1515760639	-1
1004	31	-1	0	86	1515760639	-1
1004	31	-1	0	84	1515760639	-1
1004	31	-1	0	79	1515760639	-1
1004	31	-1	0	88	1515760639	-1
1004	31	-1	0	83	1515760639	-1
1005	15	-1	0	86	1515760639	-1
1006	15	-1	0	87	1515760639	-1
1007	15	-1	0	88	1515760639	-1
1000	1	-1	0	5	1515488476	-1
1002	1	-1	0	7	1515488476	-1
1002	1	-1	0	3	1515488476	-1
1001	31	-1	0	23	1515760717	-1
1001	31	-1	0	31	1515760717	-1
1001	31	-1	0	35	1515760717	-1
1001	31	-1	0	65	1515760717	-1
1001	31	-1	0	52	1515760717	-1
1001	31	-1	0	101	1515760717	-1
1001	31	-1	0	20	1515760717	-1
1001	31	-1	0	69	1515760717	-1
1001	31	-1	0	44	1515760717	-1
1001	31	-1	0	37	1515760717	-1
1001	1	-1	0	10	1515760717	-1
1001	31	-1	0	56	1515760717	-1
1001	31	-1	0	40	1515760717	-1
1001	31	-1	0	53	1515760717	-1
1001	31	-1	0	19	1515760717	-1
1001	31	-1	0	57	1515760717	-1
1001	31	-1	0	51	1515760717	-1
1001	31	-1	0	92	1515760717	-1
1001	31	-1	0	66	1515760717	-1
1001	31	-1	0	30	1515760717	-1
1001	31	-1	0	50	1515760717	-1
1001	31	-1	0	33	1515760717	-1
1001	31	-1	0	73	1515760717	-1
1001	31	-1	0	95	1515760717	-1
1001	31	-1	0	14	1515760717	-1
1001	31	-1	0	46	1515760717	-1
1001	31	-1	0	99	1515760717	-1
1001	31	-1	0	48	1515760717	-1
1001	31	-1	0	17	1515760717	-1
1001	31	-1	0	28	1515760717	-1
1001	31	-1	0	83	1515760717	-1
1001	31	-1	0	36	1515760717	-1
1001	31	-1	0	94	1515760717	-1
1001	31	-1	0	15	1515760717	-1
1001	31	-1	0	61	1515760717	-1
1001	31	-1	0	86	1515760717	-1
1001	31	-1	0	96	1515760717	-1
1001	31	-1	0	13	1515760717	-1
1001	31	-1	0	49	1515760717	-1
1001	31	-1	0	22	1515760717	-1
1001	31	-1	0	63	1515760717	-1
1001	31	-1	0	87	1515760717	-1
1001	31	-1	0	24	1515760717	-1
1001	31	-1	0	91	1515760717	-1
1001	31	-1	0	54	1515760717	-1
1001	31	-1	0	98	1515760717	-1
1001	1	-1	0	89	1515760717	-1
1001	31	-1	0	100	1515760717	-1
1001	1	-1	0	104	1515760717	-1
1001	31	-1	0	47	1515760717	-1
1001	31	-1	0	103	1515760717	-1
1001	31	-1	0	42	1515760717	-1
1001	31	-1	0	26	1515760717	-1
1001	31	-1	0	11	1515760717	-1
1001	1	-1	0	62	1515760717	-1
1001	31	-1	0	90	1515760717	-1
1001	31	-1	0	80	1515760717	-1
1001	31	-1	0	18	1515760717	-1
1001	31	-1	0	59	1515760717	-1
1001	31	-1	0	78	1515760717	-1
1001	31	-1	0	39	1515760717	-1
1001	31	-1	0	16	1515760717	-1
1001	31	-1	0	85	1515760717	-1
1001	31	-1	0	34	1515760717	-1
1001	31	-1	0	43	1515760717	-1
1001	31	-1	0	82	1515760717	-1
1001	31	-1	0	81	1515760717	-1
1001	1	-1	0	76	1515760717	-1
1001	31	-1	0	25	1515760717	-1
1001	31	-1	0	32	1515760717	-1
1001	31	-1	0	12	1515760717	-1
1001	31	-1	0	58	1515760717	-1
1001	31	-1	0	79	1515760717	-1
1001	31	-1	0	41	1515760717	-1
1001	31	-1	0	75	1515760717	-1
1001	31	-1	0	102	1515760717	-1
1001	31	-1	0	71	1515760717	-1
1001	31	-1	0	29	1515760717	-1
1001	31	-1	0	93	1515760717	-1
1001	31	-1	0	105	1515760717	-1
1001	31	-1	0	21	1515760717	-1
1001	31	-1	0	72	1515760717	-1
1001	31	-1	0	97	1515760717	-1
1001	31	-1	0	38	1515760717	-1
1001	31	-1	0	60	1515760717	-1
1001	31	-1	0	74	1515760717	-1
1001	1	-1	0	3	1515760717	-1
1001	31	-1	0	70	1515760717	-1
1001	31	-1	0	64	1515760717	-1
1001	31	-1	0	45	1515760717	-1
1001	31	-1	0	27	1515760717	-1
1001	31	-1	0	55	1515760717	-1
1001	31	-1	0	68	1515760717	-1
1001	1	-1	0	67	1515760717	-1
1001	31	-1	0	84	1515760717	-1
1001	31	-1	0	88	1515760717	-1
1003	1	-1	0	25	1515760639	-1
1003	1	-1	0	32	1515760639	-1
1003	1	-1	0	81	1515760639	-1
1003	1	-1	0	34	1515760639	-1
1003	1	-1	0	43	1515760639	-1
1003	1	-1	0	82	1515760639	-1
1003	1	-1	0	85	1515760639	-1
1003	1	-1	0	10	1515760639	-1
1003	1	-1	0	79	1515760639	-1
1003	1	-1	0	58	1515760639	-1
1003	1	-1	0	12	1515760639	-1
1003	1	-1	0	18	1515760639	-1
1003	1	-1	0	80	1515760639	-1
1003	1	-1	0	26	1515760639	-1
1003	1	-1	0	42	1515760639	-1
1003	1	-1	0	16	1515760639	-1
1003	1	-1	0	39	1515760639	-1
1003	1	-1	0	59	1515760639	-1
1003	1	-1	0	78	1515760639	-1
1003	1	-1	0	54	1515760639	-1
1003	1	-1	0	47	1515760639	-1
1003	1	-1	0	61	1515760639	-1
1003	1	-1	0	86	1515760639	-1
1003	1	-1	0	24	1515760639	-1
1003	1	-1	0	87	1515760639	-1
1003	1	-1	0	22	1515760639	-1
1003	1	-1	0	13	1515760639	-1
1003	1	-1	0	49	1515760639	-1
1003	1	-1	0	27	1515760639	-1
1003	1	-1	0	48	1515760639	-1
1003	1	-1	0	55	1515760639	-1
1003	1	-1	0	46	1515760639	-1
1003	1	-1	0	14	1515760639	-1
1003	1	-1	0	45	1515760639	-1
1003	1	-1	0	88	1515760639	-1
1003	1	-1	0	15	1515760639	-1
1003	1	-1	0	84	1515760639	-1
1003	1	-1	0	36	1515760639	-1
1003	1	-1	0	17	1515760639	-1
1003	1	-1	0	28	1515760639	-1
1003	1	-1	0	83	1515760639	-1
1003	1	-1	0	30	1515760639	-1
1003	1	-1	0	38	1515760639	-1
1003	1	-1	0	33	1515760639	-1
1003	1	-1	0	50	1515760639	-1
1003	1	-1	0	60	1515760639	-1
1003	1	-1	0	53	1515760639	-1
1003	1	-1	0	40	1515760639	-1
1003	1	-1	0	56	1515760639	-1
1003	1	-1	0	51	1515760639	-1
1003	1	-1	0	21	1515760639	-1
1003	1	-1	0	57	1515760639	-1
1003	1	-1	0	29	1515760639	-1
1003	1	-1	0	19	1515760639	-1
1003	1	-1	0	20	1515760639	-1
1003	1	-1	0	52	1515760639	-1
1003	1	-1	0	76	1515760639	-1
1003	1	-1	0	31	1515760639	-1
1003	1	-1	0	35	1515760639	-1
1003	1	-1	0	23	1515760639	-1
1003	1	-1	0	41	1515760639	-1
1003	1	-1	0	7	1515760639	-1
1003	1	-1	0	37	1515760639	-1
1003	1	-1	0	5	1515760639	-1
1003	1	-1	0	44	1515760639	-1
1004	31	-1	0	87	1515760639	-1
1004	1	-1	0	3	1515760639	-1
1004	31	-1	0	78	1515760639	-1
1004	31	-1	0	80	1515760639	-1
1004	31	-1	0	81	1515760639	-1
1004	31	-1	0	85	1515760639	-1
1004	31	-1	0	82	1515760639	-1
1004	31	-1	0	86	1515760639	-1
1004	31	-1	0	84	1515760639	-1
1004	31	-1	0	79	1515760639	-1
1004	31	-1	0	88	1515760639	-1
1004	31	-1	0	83	1515760639	-1
1005	15	-1	0	86	1515760639	-1
1006	15	-1	0	87	1515760639	-1
1007	15	-1	0	88	1515760639	-1
1000	1	-1	0	5	1515488476	-1
1002	1	-1	0	7	1515488476	-1
1002	1	-1	0	3	1515488476	-1
1001	31	-1	0	23	1515760717	-1
1001	31	-1	0	31	1515760717	-1
1001	31	-1	0	35	1515760717	-1
1001	31	-1	0	65	1515760717	-1
1001	31	-1	0	52	1515760717	-1
1001	31	-1	0	101	1515760717	-1
1001	31	-1	0	20	1515760717	-1
1001	31	-1	0	69	1515760717	-1
1001	31	-1	0	44	1515760717	-1
1001	31	-1	0	37	1515760717	-1
1001	1	-1	0	10	1515760717	-1
1001	31	-1	0	56	1515760717	-1
1001	31	-1	0	40	1515760717	-1
1001	31	-1	0	53	1515760717	-1
1001	31	-1	0	19	1515760717	-1
1001	31	-1	0	57	1515760717	-1
1001	31	-1	0	51	1515760717	-1
1001	31	-1	0	92	1515760717	-1
1001	31	-1	0	66	1515760717	-1
1001	31	-1	0	30	1515760717	-1
1001	31	-1	0	50	1515760717	-1
1001	31	-1	0	33	1515760717	-1
1001	31	-1	0	73	1515760717	-1
1001	31	-1	0	95	1515760717	-1
1001	31	-1	0	14	1515760717	-1
1001	31	-1	0	46	1515760717	-1
1001	31	-1	0	99	1515760717	-1
1001	31	-1	0	48	1515760717	-1
1001	31	-1	0	17	1515760717	-1
1001	31	-1	0	28	1515760717	-1
1001	31	-1	0	83	1515760717	-1
1001	31	-1	0	36	1515760717	-1
1001	31	-1	0	94	1515760717	-1
1001	31	-1	0	15	1515760717	-1
1001	31	-1	0	61	1515760717	-1
1001	31	-1	0	86	1515760717	-1
1001	31	-1	0	96	1515760717	-1
1001	31	-1	0	13	1515760717	-1
1001	31	-1	0	49	1515760717	-1
1001	31	-1	0	22	1515760717	-1
1001	31	-1	0	63	1515760717	-1
1001	31	-1	0	87	1515760717	-1
1001	31	-1	0	24	1515760717	-1
1001	31	-1	0	91	1515760717	-1
1001	31	-1	0	54	1515760717	-1
1001	31	-1	0	98	1515760717	-1
1001	1	-1	0	89	1515760717	-1
1001	31	-1	0	100	1515760717	-1
1001	1	-1	0	104	1515760717	-1
1001	31	-1	0	47	1515760717	-1
1001	31	-1	0	103	1515760717	-1
1001	31	-1	0	42	1515760717	-1
1001	31	-1	0	26	1515760717	-1
1001	31	-1	0	11	1515760717	-1
1001	1	-1	0	62	1515760717	-1
1001	31	-1	0	90	1515760717	-1
1001	31	-1	0	80	1515760717	-1
1001	31	-1	0	18	1515760717	-1
1001	31	-1	0	59	1515760717	-1
1001	31	-1	0	78	1515760717	-1
1001	31	-1	0	39	1515760717	-1
1001	31	-1	0	16	1515760717	-1
1001	31	-1	0	85	1515760717	-1
1001	31	-1	0	34	1515760717	-1
1001	31	-1	0	43	1515760717	-1
1001	31	-1	0	82	1515760717	-1
1001	31	-1	0	81	1515760717	-1
1001	1	-1	0	76	1515760717	-1
1001	31	-1	0	25	1515760717	-1
1001	31	-1	0	32	1515760717	-1
1001	31	-1	0	12	1515760717	-1
1001	31	-1	0	58	1515760717	-1
1001	31	-1	0	79	1515760717	-1
1001	31	-1	0	41	1515760717	-1
1001	31	-1	0	75	1515760717	-1
1001	31	-1	0	102	1515760717	-1
1001	31	-1	0	71	1515760717	-1
1001	31	-1	0	29	1515760717	-1
1001	31	-1	0	93	1515760717	-1
1001	31	-1	0	105	1515760717	-1
1001	31	-1	0	21	1515760717	-1
1001	31	-1	0	72	1515760717	-1
1001	31	-1	0	97	1515760717	-1
1001	31	-1	0	38	1515760717	-1
1001	31	-1	0	60	1515760717	-1
1001	31	-1	0	74	1515760717	-1
1001	1	-1	0	3	1515760717	-1
1001	31	-1	0	70	1515760717	-1
1001	31	-1	0	64	1515760717	-1
1001	31	-1	0	45	1515760717	-1
1001	31	-1	0	27	1515760717	-1
1001	31	-1	0	55	1515760717	-1
1001	31	-1	0	68	1515760717	-1
1001	1	-1	0	67	1515760717	-1
1001	31	-1	0	84	1515760717	-1
1001	31	-1	0	88	1515760717	-1
1003	1	-1	0	25	1515760639	-1
1003	1	-1	0	32	1515760639	-1
1003	1	-1	0	81	1515760639	-1
1003	1	-1	0	34	1515760639	-1
1003	1	-1	0	43	1515760639	-1
1003	1	-1	0	82	1515760639	-1
1003	1	-1	0	85	1515760639	-1
1003	1	-1	0	10	1515760639	-1
1003	1	-1	0	79	1515760639	-1
1003	1	-1	0	58	1515760639	-1
1003	1	-1	0	12	1515760639	-1
1003	1	-1	0	18	1515760639	-1
1003	1	-1	0	80	1515760639	-1
1003	1	-1	0	26	1515760639	-1
1003	1	-1	0	42	1515760639	-1
1003	1	-1	0	16	1515760639	-1
1003	1	-1	0	39	1515760639	-1
1003	1	-1	0	59	1515760639	-1
1003	1	-1	0	78	1515760639	-1
1003	1	-1	0	54	1515760639	-1
1003	1	-1	0	47	1515760639	-1
1003	1	-1	0	61	1515760639	-1
1003	1	-1	0	86	1515760639	-1
1003	1	-1	0	24	1515760639	-1
1003	1	-1	0	87	1515760639	-1
1003	1	-1	0	22	1515760639	-1
1003	1	-1	0	13	1515760639	-1
1003	1	-1	0	49	1515760639	-1
1003	1	-1	0	27	1515760639	-1
1003	1	-1	0	48	1515760639	-1
1003	1	-1	0	55	1515760639	-1
1003	1	-1	0	46	1515760639	-1
1003	1	-1	0	14	1515760639	-1
1003	1	-1	0	45	1515760639	-1
1003	1	-1	0	88	1515760639	-1
1003	1	-1	0	15	1515760639	-1
1003	1	-1	0	84	1515760639	-1
1003	1	-1	0	36	1515760639	-1
1003	1	-1	0	17	1515760639	-1
1003	1	-1	0	28	1515760639	-1
1003	1	-1	0	83	1515760639	-1
1003	1	-1	0	30	1515760639	-1
1003	1	-1	0	38	1515760639	-1
1003	1	-1	0	33	1515760639	-1
1003	1	-1	0	50	1515760639	-1
1003	1	-1	0	60	1515760639	-1
1003	1	-1	0	53	1515760639	-1
1003	1	-1	0	40	1515760639	-1
1003	1	-1	0	56	1515760639	-1
1003	1	-1	0	51	1515760639	-1
1003	1	-1	0	21	1515760639	-1
1003	1	-1	0	57	1515760639	-1
1003	1	-1	0	29	1515760639	-1
1003	1	-1	0	19	1515760639	-1
1003	1	-1	0	20	1515760639	-1
1003	1	-1	0	52	1515760639	-1
1003	1	-1	0	76	1515760639	-1
1003	1	-1	0	31	1515760639	-1
1003	1	-1	0	35	1515760639	-1
1003	1	-1	0	23	1515760639	-1
1003	1	-1	0	41	1515760639	-1
1003	1	-1	0	7	1515760639	-1
1003	1	-1	0	37	1515760639	-1
1003	1	-1	0	5	1515760639	-1
1003	1	-1	0	44	1515760639	-1
1004	31	-1	0	87	1515760639	-1
1004	1	-1	0	3	1515760639	-1
1004	31	-1	0	78	1515760639	-1
1004	31	-1	0	80	1515760639	-1
1004	31	-1	0	81	1515760639	-1
1004	31	-1	0	85	1515760639	-1
1004	31	-1	0	82	1515760639	-1
1004	31	-1	0	86	1515760639	-1
1004	31	-1	0	84	1515760639	-1
1004	31	-1	0	79	1515760639	-1
1004	31	-1	0	88	1515760639	-1
1004	31	-1	0	83	1515760639	-1
1005	15	-1	0	86	1515760639	-1
1006	15	-1	0	87	1515760639	-1
1007	15	-1	0	88	1515760639	-1
1000	1	-1	0	5	1515488476	-1
1002	1	-1	0	7	1515488476	-1
1002	1	-1	0	3	1515488476	-1
1001	31	-1	0	23	1515760717	-1
1001	31	-1	0	31	1515760717	-1
1001	31	-1	0	35	1515760717	-1
1001	31	-1	0	65	1515760717	-1
1001	31	-1	0	52	1515760717	-1
1001	31	-1	0	101	1515760717	-1
1001	31	-1	0	20	1515760717	-1
1001	31	-1	0	69	1515760717	-1
1001	31	-1	0	44	1515760717	-1
1001	31	-1	0	37	1515760717	-1
1001	1	-1	0	10	1515760717	-1
1001	31	-1	0	56	1515760717	-1
1001	31	-1	0	40	1515760717	-1
1001	31	-1	0	53	1515760717	-1
1001	31	-1	0	19	1515760717	-1
1001	31	-1	0	57	1515760717	-1
1001	31	-1	0	51	1515760717	-1
1001	31	-1	0	92	1515760717	-1
1001	31	-1	0	66	1515760717	-1
1001	31	-1	0	30	1515760717	-1
1001	31	-1	0	50	1515760717	-1
1001	31	-1	0	33	1515760717	-1
1001	31	-1	0	73	1515760717	-1
1001	31	-1	0	95	1515760717	-1
1001	31	-1	0	14	1515760717	-1
1001	31	-1	0	46	1515760717	-1
1001	31	-1	0	99	1515760717	-1
1001	31	-1	0	48	1515760717	-1
1001	31	-1	0	17	1515760717	-1
1001	31	-1	0	28	1515760717	-1
1001	31	-1	0	83	1515760717	-1
1001	31	-1	0	36	1515760717	-1
1001	31	-1	0	94	1515760717	-1
1001	31	-1	0	15	1515760717	-1
1001	31	-1	0	61	1515760717	-1
1001	31	-1	0	86	1515760717	-1
1001	31	-1	0	96	1515760717	-1
1001	31	-1	0	13	1515760717	-1
1001	31	-1	0	49	1515760717	-1
1001	31	-1	0	22	1515760717	-1
1001	31	-1	0	63	1515760717	-1
1001	31	-1	0	87	1515760717	-1
1001	31	-1	0	24	1515760717	-1
1001	31	-1	0	91	1515760717	-1
1001	31	-1	0	54	1515760717	-1
1001	31	-1	0	98	1515760717	-1
1001	1	-1	0	89	1515760717	-1
1001	31	-1	0	100	1515760717	-1
1001	1	-1	0	104	1515760717	-1
1001	31	-1	0	47	1515760717	-1
1001	31	-1	0	103	1515760717	-1
1001	31	-1	0	42	1515760717	-1
1001	31	-1	0	26	1515760717	-1
1001	31	-1	0	11	1515760717	-1
1001	1	-1	0	62	1515760717	-1
1001	31	-1	0	90	1515760717	-1
1001	31	-1	0	80	1515760717	-1
1001	31	-1	0	18	1515760717	-1
1001	31	-1	0	59	1515760717	-1
1001	31	-1	0	78	1515760717	-1
1001	31	-1	0	39	1515760717	-1
1001	31	-1	0	16	1515760717	-1
1001	31	-1	0	85	1515760717	-1
1001	31	-1	0	34	1515760717	-1
1001	31	-1	0	43	1515760717	-1
1001	31	-1	0	82	1515760717	-1
1001	31	-1	0	81	1515760717	-1
1001	1	-1	0	76	1515760717	-1
1001	31	-1	0	25	1515760717	-1
1001	31	-1	0	32	1515760717	-1
1001	31	-1	0	12	1515760717	-1
1001	31	-1	0	58	1515760717	-1
1001	31	-1	0	79	1515760717	-1
1001	31	-1	0	41	1515760717	-1
1001	31	-1	0	75	1515760717	-1
1001	31	-1	0	102	1515760717	-1
1001	31	-1	0	71	1515760717	-1
1001	31	-1	0	29	1515760717	-1
1001	31	-1	0	93	1515760717	-1
1001	31	-1	0	105	1515760717	-1
1001	31	-1	0	21	1515760717	-1
1001	31	-1	0	72	1515760717	-1
1001	31	-1	0	97	1515760717	-1
1001	31	-1	0	38	1515760717	-1
1001	31	-1	0	60	1515760717	-1
1001	31	-1	0	74	1515760717	-1
1001	1	-1	0	3	1515760717	-1
1001	31	-1	0	70	1515760717	-1
1001	31	-1	0	64	1515760717	-1
1001	31	-1	0	45	1515760717	-1
1001	31	-1	0	27	1515760717	-1
1001	31	-1	0	55	1515760717	-1
1001	31	-1	0	68	1515760717	-1
1001	1	-1	0	67	1515760717	-1
1001	31	-1	0	84	1515760717	-1
1001	31	-1	0	88	1515760717	-1
1003	1	-1	0	25	1515760639	-1
1003	1	-1	0	32	1515760639	-1
1003	1	-1	0	81	1515760639	-1
1003	1	-1	0	34	1515760639	-1
1003	1	-1	0	43	1515760639	-1
1003	1	-1	0	82	1515760639	-1
1003	1	-1	0	85	1515760639	-1
1003	1	-1	0	10	1515760639	-1
1003	1	-1	0	79	1515760639	-1
1003	1	-1	0	58	1515760639	-1
1003	1	-1	0	12	1515760639	-1
1003	1	-1	0	18	1515760639	-1
1003	1	-1	0	80	1515760639	-1
1003	1	-1	0	26	1515760639	-1
1003	1	-1	0	42	1515760639	-1
1003	1	-1	0	16	1515760639	-1
1003	1	-1	0	39	1515760639	-1
1003	1	-1	0	59	1515760639	-1
1003	1	-1	0	78	1515760639	-1
1003	1	-1	0	54	1515760639	-1
1003	1	-1	0	47	1515760639	-1
1003	1	-1	0	61	1515760639	-1
1003	1	-1	0	86	1515760639	-1
1003	1	-1	0	24	1515760639	-1
1003	1	-1	0	87	1515760639	-1
1003	1	-1	0	22	1515760639	-1
1003	1	-1	0	13	1515760639	-1
1003	1	-1	0	49	1515760639	-1
1003	1	-1	0	27	1515760639	-1
1003	1	-1	0	48	1515760639	-1
1003	1	-1	0	55	1515760639	-1
1003	1	-1	0	46	1515760639	-1
1003	1	-1	0	14	1515760639	-1
1003	1	-1	0	45	1515760639	-1
1003	1	-1	0	88	1515760639	-1
1003	1	-1	0	15	1515760639	-1
1003	1	-1	0	84	1515760639	-1
1003	1	-1	0	36	1515760639	-1
1003	1	-1	0	17	1515760639	-1
1003	1	-1	0	28	1515760639	-1
1003	1	-1	0	83	1515760639	-1
1003	1	-1	0	30	1515760639	-1
1003	1	-1	0	38	1515760639	-1
1003	1	-1	0	33	1515760639	-1
1003	1	-1	0	50	1515760639	-1
1003	1	-1	0	60	1515760639	-1
1003	1	-1	0	53	1515760639	-1
1003	1	-1	0	40	1515760639	-1
1003	1	-1	0	56	1515760639	-1
1003	1	-1	0	51	1515760639	-1
1003	1	-1	0	21	1515760639	-1
1003	1	-1	0	57	1515760639	-1
1003	1	-1	0	29	1515760639	-1
1003	1	-1	0	19	1515760639	-1
1003	1	-1	0	20	1515760639	-1
1003	1	-1	0	52	1515760639	-1
1003	1	-1	0	76	1515760639	-1
1003	1	-1	0	31	1515760639	-1
1003	1	-1	0	35	1515760639	-1
1003	1	-1	0	23	1515760639	-1
1003	1	-1	0	41	1515760639	-1
1003	1	-1	0	7	1515760639	-1
1003	1	-1	0	37	1515760639	-1
1003	1	-1	0	5	1515760639	-1
1003	1	-1	0	44	1515760639	-1
1004	31	-1	0	87	1515760639	-1
1004	1	-1	0	3	1515760639	-1
1004	31	-1	0	78	1515760639	-1
1004	31	-1	0	80	1515760639	-1
1004	31	-1	0	81	1515760639	-1
1004	31	-1	0	85	1515760639	-1
1004	31	-1	0	82	1515760639	-1
1004	31	-1	0	86	1515760639	-1
1004	31	-1	0	84	1515760639	-1
1004	31	-1	0	79	1515760639	-1
1004	31	-1	0	88	1515760639	-1
1004	31	-1	0	83	1515760639	-1
1005	15	-1	0	86	1515760639	-1
1006	15	-1	0	87	1515760639	-1
1007	15	-1	0	88	1515760639	-1
\.


--
-- Data for Name: phpgw_applications; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_applications (app_id, app_name, app_enabled, app_order, app_tables, app_version) FROM stdin;
2	admin	1	1		0.9.17.001
3	preferences	1	1		0.9.17.501
5	addressbook	1	4		0.9.17.502
6	controller	1	100	controller_control,controller_control_item_list,controller_control_item,controller_control_group,controller_check_item,controller_check_list,controller_procedure,controller_control_group_list,controller_control_location_list,controller_control_component_list,controller_control_serie,controller_control_serie_history,controller_control_group_component_list,controller_document,controller_document_types,controller_check_item_case,controller_check_item_status,controller_control_item_option	0.1.55
7	rental	1	51	rental_party,rental_contract,rental_contract_composite,rental_contract_party,rental_composite,rental_location_factor,rental_composite_type,rental_composite_standard,rental_contract_price_item,rental_contract_responsibility_unit,rental_billing,rental_invoice,rental_invoice_price_item,rental_unit,rental_document,rental_document_types,rental_contract_last_edited,rental_contract_responsibility,rental_notification,rental_notification_workbench,rental_billing_term,rental_price_item,rental_contract_types,rental_billing_info,rental_adjustment,rental_application,rental_application_comment,rental_application_composite,rental_moveout,rental_moveout_comment,rental_movein,rental_movein_comment,rental_email_out,rental_email_out_party,rental_email_template	0.1.0.39
8	frontend	1	9		0.6
9	mobilefrontend	1	80		0.1.2
4	property	1	8	fm_district,fm_part_of_town,fm_gab_location,fm_streetaddress,fm_tenant,fm_tenant_category,fm_vendor,fm_vendor_category,fm_locations,fm_location1_category,fm_location1,fm_location1_history,fm_location2_category,fm_location2,fm_location2_history,fm_location3_category,fm_location3,fm_location3_history,fm_location4_category,fm_location4,fm_location4_history,fm_location_type,fm_location_config,fm_location_contact,fm_location_exception,fm_location_exception_severity,fm_location_exception_category,fm_location_exception_category_text,fm_building_part,fm_b_account,fm_b_account_category,fm_workorder,fm_workorder_budget,fm_workorder_history,fm_workorder_status,fm_activities,fm_agreement_group,fm_agreement,fm_agreement_status,fm_activity_price_index,fm_branch,fm_wo_hours,fm_wo_hours_category,fm_wo_h_deviation,fm_key_loc,fm_authorities_demands,fm_condition_survey_status,fm_condition_survey_history,fm_condition_survey,fm_request,fm_request_responsible_unit,fm_request_condition_type,fm_request_condition,fm_request_status,fm_request_history,fm_request_consume,fm_request_planning,fm_template,fm_template_hours,fm_chapter,fm_ns3420,fm_project_status,fm_project,fm_project_buffer_budget,fm_projectbranch,fm_external_project,fm_unspsc_code,fm_project_history,fm_project_budget,fm_tts_status,fm_tts_priority,fm_tts_tickets,fm_tts_history,fm_tts_views,fm_tts_payments,fm_tts_budget,fm_org_unit,fm_ecoart,fm_ecoavvik,fm_ecobilag_process_code,fm_ecobilag_process_log,fm_ecobilag,fm_ecobilagkilde,fm_ecobilagoverf,fm_ecobilag_category,fm_eco_service,fm_ecodimb,fm_ecodimb_role,fm_ecodimb_role_user,fm_ecodimb_role_user_substitute,fm_ecodimd,fm_ecologg,fm_ecomva,fm_ecouser,fm_eco_periodization,fm_eco_periodization_outline,fm_eco_period_transition,fm_event,fm_event_action,fm_event_exception,fm_event_schedule,fm_investment,fm_investment_value,fm_event_receipt,fm_idgenerator,fm_document,fm_document_relation,fm_document_history,fm_document_status,fm_standard_unit,fm_owner,fm_owner_category,fm_cache,fm_entity,fm_entity_category,fm_entity_lookup,fm_entity_history,fm_entity_group,fm_custom,fm_custom_cols,fm_orders,fm_order_dim1,fm_order_template,fm_response_template,fm_s_agreement,fm_s_agreement_budget,fm_s_agreement_category,fm_s_agreement_detail,fm_s_agreement_pricing,fm_s_agreement_history,fm_async_method,fm_cron_log,fm_tenant_claim,fm_tenant_claim_category,fm_tenant_claim_history,fm_budget_basis,fm_budget,fm_budget_period,fm_budget_cost,fm_responsibility,fm_responsibility_role,fm_responsibility_contact,fm_responsibility_module,fm_action_pending,fm_action_pending_category,fm_jasper,fm_jasper_input_type,fm_jasper_format_type,fm_jasper_input,fm_custom_menu_items,fm_regulations,fm_generic_history,fm_view_dataset,fm_view_dataset_report	0.9.17.726
1	phpgwapi	3	1	phpgw_access_log,phpgw_accounts,phpgw_accounts_data,phpgw_account_delegates,phpgw_acl,phpgw_applications,phpgw_app_sessions,phpgw_async,phpgw_cache_user,phpgw_categories,phpgw_config,phpgw_contact,phpgw_contact_addr,phpgw_contact_addr_type,phpgw_contact_comm,phpgw_contact_comm_descr,phpgw_contact_comm_type,phpgw_contact_note,phpgw_contact_note_type,phpgw_contact_org,phpgw_contact_org_person,phpgw_contact_others,phpgw_contact_person,phpgw_contact_types,phpgw_cust_attribute_group,phpgw_cust_attribute,phpgw_cust_choice,phpgw_cust_function,phpgw_group_map,phpgw_history_log,phpgw_hooks,phpgw_interlink,phpgw_interserv,phpgw_lang,phpgw_languages,phpgw_locations,phpgw_log,phpgw_mail_handler,phpgw_mapping,phpgw_nextid,phpgw_preferences,phpgw_sessions,phpgw_vfs,phpgw_vfs_filedata,phpgw_vfs_file_relation,phpgw_config2_section,phpgw_config2_attrib,phpgw_config2_choice,phpgw_config2_value,phpgw_notification	0.9.17.558
\.


--
-- Data for Name: phpgw_async; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_async (id, next, times, method, data, account_id) FROM stdin;
rental_populate_workbench_notifications	1517616000	a:1:{s:3:"day";s:3:"*/1";}	rental.sonotification.populate_workbench_notifications	a:1:{s:4:"time";i:1517616000;}	0
rental_run_adjustments	1517616000	a:1:{s:3:"day";s:3:"*/1";}	rental.soadjustment.run_adjustments	a:1:{s:4:"time";i:1517616000;}	0
\.


--
-- Data for Name: phpgw_cache_user; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_cache_user (item_key, user_id, cache_data, lastmodts) FROM stdin;
\.


--
-- Data for Name: phpgw_categories; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_categories (cat_id, cat_main, cat_parent, cat_level, cat_owner, cat_access, cat_appname, cat_name, cat_description, cat_data, last_mod, location_id, active) FROM stdin;
1	1	0	0	-1	public	property	Picture	Picture		1515760210	30	0
2	2	0	0	-1	public	property	Report	Report		1515760210	30	0
3	3	0	0	-1	public	property	Instruction	Instruction		1515760210	30	0
4	4	0	0	-1	public	property	Test category 1	Description of category 1		1517584295	26	1
5	5	0	0	-1	public	property	Test category 2	Description of category 2		1517584335	26	1
\.


--
-- Data for Name: phpgw_config; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_config (config_app, config_name, config_value) FROM stdin;
phpgwapi	max_access_log_age	90
phpgwapi	block_time	30
phpgwapi	num_unsuccessful_id	3
phpgwapi	num_unsuccessful_ip	3
phpgwapi	install_id	49a36a64042c01dccf7cbe3403f31a4a4d7297b0
phpgwapi	max_history	20
phpgwapi	sessions_checkip	True
phpgwapi	sessions_timeout	1440
phpgwapi	addressmaster	-3
phpgwapi	log_levels	a:3:{s:12:"global_level";s:1:"E";s:6:"module";a:0:{}s:4:"user";a:0:{}}
phpgwapi	usecookies	True
phpgwapi	temp_dir	/tmp
phpgwapi	files_dir	/var/www/portico-empty-start-data
phpgwapi	webserver_url	/portico
phpgwapi	hostname	127.0.0.1:8080
phpgwapi	cookie_domain	
phpgwapi	daytime_port	00
phpgwapi	auth_type	sql
phpgwapi	account_repository	sql
phpgwapi	account_min_id	1000
phpgwapi	account_max_id	65535
phpgwapi	group_min_id	500
phpgwapi	group_max_id	999
phpgwapi	auto_create_expire	604800
phpgwapi	acl_default	deny
phpgwapi	encryption_type	SSHA
phpgwapi	password_level	NONALPHA
phpgwapi	ldap_account_home	/noexistant
phpgwapi	ldap_account_shell	/bin/false
phpgwapi	ldap_host	localhost
phpgwapi	mapping	id
phpgwapi	encryptkey	013f77e7221f293248b4c9e494748fa2
phpgwapi	mcrypt_algo	tripledes
phpgwapi	mcrypt_mode	cbc
phpgwapi	file_repository	sql
phpgwapi	file_store_contents	filesystem
phpgwapi	lang_ctimes	a:2:{s:2:"en";a:3:{s:8:"phpgwapi";i:1515162875;s:5:"admin";i:1515162867;s:11:"preferences";i:1515162918;}s:2:"no";a:3:{s:8:"phpgwapi";i:1515162875;s:5:"admin";i:1515162867;s:11:"preferences";i:1515162918;}}
mobilefrontend	usecookies	True
\.


--
-- Data for Name: phpgw_config2_attrib; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_config2_attrib (section_id, id, input_type, name, descr) FROM stdin;
1	1	text	host	Host
1	2	text	user	User
1	3	password	password	Password
1	4	listbox	method	Export / import method
1	5	listbox	invoice_approval	Number of persons required to approve for payment
1	6	text	baseurl_invoice	baseurl on remote server for image of invoice
2	1	text	local_path	path on local sever to store imported files
2	2	text	budget_responsible	default initials if responsible can not be found
2	3	text	remote_basedir	basedir on remote server
3	1	text	cleanup_old	Overføre manuelt registrerte fakturaer rett til historikk
3	2	date	dato_aarsavslutning	Dato for årsavslutning: overført pr. desember foregående år
3	3	text	path	path on local sever to store exported files
3	4	text	pre_path	path on local sever to store exported files for pre approved vouchers
3	5	text	remote_basedir	basedir on remote server to receive files
\.


--
-- Data for Name: phpgw_config2_choice; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_config2_choice (section_id, attrib_id, id, value) FROM stdin;
1	4	1	local
1	4	2	ftp
1	4	3	ssh
1	5	1	1
1	5	2	2
\.


--
-- Data for Name: phpgw_config2_section; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_config2_section (id, location_id, name, descr, data) FROM stdin;
1	29	common	common invoice config	\N
2	29	import	import invoice config	\N
3	29	export	Invoice export	\N
\.


--
-- Data for Name: phpgw_config2_value; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_config2_value (section_id, attrib_id, id, value) FROM stdin;
\.


--
-- Data for Name: phpgw_contact; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact (contact_id, owner, access, cat_id, contact_type_id) FROM stdin;
1	-3	public		2
2	-3	public		2
3	-3	public		1
4	-3	public		2
5	-3	public		1
6	-3	public		1
7	-3	public		1
8	-3	public		1
9	-3	public		1
\.


--
-- Data for Name: phpgw_contact_addr; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_addr (contact_addr_id, contact_id, addr_type_id, add1, add2, add3, city, state, postal_code, country, tz, preferred, created_on, created_by, modified_on, modified_by) FROM stdin;
\.


--
-- Data for Name: phpgw_contact_addr_type; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_addr_type (addr_type_id, description) FROM stdin;
1	work
2	home
\.


--
-- Data for Name: phpgw_contact_comm; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_comm (comm_id, contact_id, comm_descr_id, preferred, comm_data, created_on, created_by, modified_on, modified_by) FROM stdin;
\.


--
-- Data for Name: phpgw_contact_comm_descr; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_comm_descr (comm_descr_id, comm_type_id, descr) FROM stdin;
1	1	home email
2	1	work email
3	2	home phone
4	2	work phone
5	2	voice phone
6	2	msg phone
7	2	pager
8	2	bbs
9	2	modem
10	2	isdn
11	2	video
12	4	home fax
13	4	work fax
14	3	mobile (cell) phone
15	3	car phone
16	5	msn
17	5	aim
18	5	yahoo
19	5	icq
20	5	jabber
21	6	website
\.


--
-- Data for Name: phpgw_contact_comm_type; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_comm_type (comm_type_id, type, active, class) FROM stdin;
1	email	\N	\N
2	phone	\N	\N
3	mobile phone	\N	\N
4	fax	\N	\N
5	instant messaging	\N	\N
6	url	\N	\N
7	other	\N	\N
\.


--
-- Data for Name: phpgw_contact_note; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_note (contact_note_id, contact_id, note_type_id, note_text, created_on, created_by, modified_on, modified_by) FROM stdin;
\.


--
-- Data for Name: phpgw_contact_note_type; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_note_type (note_type_id, description) FROM stdin;
1	general
2	vcard
3	system
\.


--
-- Data for Name: phpgw_contact_org; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_org (org_id, name, active, parent, created_on, created_by, modified_on, modified_by) FROM stdin;
1	!Default Group	Y	\N	1515488476	0	1515488476	0
2	!Admins Group	Y	\N	1515488476	0	1515488476	0
4	!Rental Group	Y	\N	1515760639	0	1515760639	0
1	!Default Group	Y	\N	1515488476	0	1515488476	0
2	!Admins Group	Y	\N	1515488476	0	1515488476	0
4	!Rental Group	Y	\N	1515760639	0	1515760639	0
1	!Default Group	Y	\N	1515488476	0	1515488476	0
2	!Admins Group	Y	\N	1515488476	0	1515488476	0
4	!Rental Group	Y	\N	1515760639	0	1515760639	0
1	!Default Group	Y	\N	1515488476	0	1515488476	0
2	!Admins Group	Y	\N	1515488476	0	1515488476	0
4	!Rental Group	Y	\N	1515760639	0	1515760639	0
1	!Default Group	Y	\N	1515488476	0	1515488476	0
2	!Admins Group	Y	\N	1515488476	0	1515488476	0
4	!Rental Group	Y	\N	1515760639	0	1515760639	0
1	!Default Group	Y	\N	1515488476	0	1515488476	0
2	!Admins Group	Y	\N	1515488476	0	1515488476	0
4	!Rental Group	Y	\N	1515760639	0	1515760639	0
1	!Default Group	Y	\N	1515488476	0	1515488476	0
2	!Admins Group	Y	\N	1515488476	0	1515488476	0
4	!Rental Group	Y	\N	1515760639	0	1515760639	0
1	!Default Group	Y	\N	1515488476	0	1515488476	0
2	!Admins Group	Y	\N	1515488476	0	1515488476	0
4	!Rental Group	Y	\N	1515760639	0	1515760639	0
\.


--
-- Data for Name: phpgw_contact_org_person; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_org_person (org_id, person_id, addr_id, preferred, created_on, created_by) FROM stdin;
\.


--
-- Data for Name: phpgw_contact_others; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_others (other_id, contact_id, contact_owner, other_name, other_value) FROM stdin;
\.


--
-- Data for Name: phpgw_contact_person; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_person (person_id, first_name, last_name, middle_name, prefix, suffix, birthday, pubkey, title, department, initials, sound, active, created_on, created_by, modified_on, modified_by) FROM stdin;
3	System	Administrator	\N	\N	\N	\N	\N	\N	\N	\N	\N	Y	1515488476	0	1515488476	0
5	Rental	Administrator	\N	\N	\N	\N	\N	\N	\N	\N	\N	Y	1515760639	0	1515760639	0
6	Rental	Internal	\N	\N	\N	\N	\N	\N	\N	\N	\N	Y	1515760639	0	1515760639	0
7	Rental	In	\N	\N	\N	\N	\N	\N	\N	\N	\N	Y	1515760639	0	1515760639	0
8	Rental	Out	\N	\N	\N	\N	\N	\N	\N	\N	\N	Y	1515760639	0	1515760639	0
9	Rental	Manager	\N	\N	\N	\N	\N	\N	\N	\N	\N	Y	1515760639	0	1515760639	0
\.


--
-- Data for Name: phpgw_contact_types; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_contact_types (contact_type_id, contact_type_descr, contact_type_table) FROM stdin;
1	Persons	phpgw_contact_person
2	Organizations	phpgw_contact_org
\.


--
-- Data for Name: phpgw_cust_attribute; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_cust_attribute (location_id, group_id, id, column_name, input_text, statustext, datatype, search, history, list, attrib_sort, size, precision_, scale, default_value, nullable, disabled, lookup_form, custom, helpmsg, get_list_function, get_list_function_input, get_single_function, get_single_function_input, short_description, javascript_action) FROM stdin;
44	0	1	abid	Contact	Contakt person	AB	\N	\N	1	1	\N	4	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
44	0	2	org_name	Name	The name of the owner	V	1	\N	1	2	\N	50	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
44	0	3	remark	remark	remark	T	\N	\N	1	3	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
43	0	1	first_name	First name	First name	V	1	\N	1	1	\N	50	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
43	0	2	last_name	Last name	Last name	V	1	\N	1	2	\N	50	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
43	0	3	contact_phone	contact phone	contact phone	V	1	\N	1	3	\N	20	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
43	0	4	phpgw_account_id	Mapped User	Mapped User	user	\N	\N	\N	4	\N	4	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
43	0	5	account_lid	User Name	User name for login	V	\N	\N	\N	5	\N	25	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
43	0	6	account_pwd	Password	Users Password	pwd	\N	\N	\N	6	\N	32	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
43	0	7	account_status	account status	account status	LB	\N	\N	\N	7	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
45	0	1	org_name	Name	The Name of the vendor	V	1	\N	1	1	\N	50	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
45	0	2	contact_phone	Contact phone	Contact phone	V	1	\N	1	2	\N	20	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
45	0	3	email	email	email	email	1	\N	1	3	\N	64	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
17	0	1	location_code	location_code	location_code	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	2	loc1_name	loc1_name	loc1_name	V	\N	\N	\N	\N	\N	50	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	3	entry_date	entry_date	entry_date	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	4	category	category	category	I	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	5	user_id	user_id	user_id	I	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	6	modified_by	modified_by	modified_by	user	\N	\N	\N	\N	\N	4	\N	\N	true	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	7	modified_on	modified_on	modified_on	DT	\N	\N	\N	\N	\N	8	\N	\N	true	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	8	loc1	loc1	loc1	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	1	location_code	location_code	location_code	V	\N	\N	\N	\N	\N	8	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	2	loc2_name	loc2_name	loc2_name	V	\N	\N	\N	\N	\N	50	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	3	entry_date	entry_date	entry_date	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	4	category	category	category	I	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	5	user_id	user_id	user_id	I	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	6	modified_by	modified_by	modified_by	user	\N	\N	\N	\N	\N	4	\N	\N	true	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	7	modified_on	modified_on	modified_on	DT	\N	\N	\N	\N	\N	8	\N	\N	true	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	8	loc1	loc1	loc1	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	9	loc2	loc2	loc2	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	1	location_code	location_code	location_code	V	\N	\N	\N	\N	\N	12	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	2	loc3_name	loc3_name	loc3_name	V	\N	\N	\N	\N	\N	50	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	3	entry_date	entry_date	entry_date	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	4	category	category	category	I	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	5	user_id	user_id	user_id	I	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	6	modified_by	modified_by	modified_by	user	\N	\N	\N	\N	\N	4	\N	\N	true	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	7	modified_on	modified_on	modified_on	DT	\N	\N	\N	\N	\N	8	\N	\N	true	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	8	loc1	loc1	loc1	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	9	loc2	loc2	loc2	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	10	loc3	loc3	loc3	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	1	location_code	location_code	location_code	V	\N	\N	\N	\N	\N	16	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	2	loc4_name	loc4_name	loc4_name	V	\N	\N	\N	\N	\N	50	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	3	entry_date	entry_date	entry_date	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	4	category	category	category	I	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	5	user_id	user_id	user_id	I	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	6	modified_by	modified_by	modified_by	user	\N	\N	\N	\N	\N	4	\N	\N	true	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	7	modified_on	modified_on	modified_on	DT	\N	\N	\N	\N	\N	8	\N	\N	true	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	8	loc1	loc1	loc1	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	9	loc2	loc2	loc2	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	10	loc3	loc3	loc3	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	11	loc4	loc4	loc4	V	\N	\N	\N	\N	\N	4	\N	\N	False	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	10	status	Status	Status	LB	\N	\N	\N	1	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
17	0	11	remark	Remark	Remark	T	\N	\N	\N	2	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
17	0	12	mva	mva	Status	I	\N	\N	\N	3	\N	4	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
17	0	13	kostra_id	kostra_id	kostra_id	I	\N	\N	\N	4	\N	4	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
17	0	14	part_of_town_id	part_of_town_id	part_of_town_id	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	15	owner_id	owner_id	owner_id	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	16	change_type	change_type	change_type	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	0	17	rental_area	Rental area	Rental area	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
17	0	18	area_gross	Gross area	Sum of the areas included within the outside face of the exterior walls of a building.	N	\N	\N	\N	6	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
17	0	19	area_net	Net area	The wall-to-wall floor area of a room.	N	\N	\N	\N	7	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
17	0	20	area_usable	Usable area	generally measured from paint to paint inside the permanent walls and to the middle of partitions separating rooms	N	\N	\N	\N	8	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
17	0	21	delivery_address	Delivery address	Delivery address	T	\N	\N	\N	9	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
18	0	11	status	Status	Status	LB	\N	\N	\N	1	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
18	0	12	remark	Remark	Remark	T	\N	\N	\N	2	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
18	0	13	change_type	change_type	change_type	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	0	14	rental_area	Rental area	Rental area	N	\N	\N	\N	3	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
18	0	15	area_gross	Gross area	Sum of the areas included within the outside face of the exterior walls of a building.	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
18	0	16	area_net	Net area	The wall-to-wall floor area of a room.	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
18	0	17	area_usable	Usable area	generally measured from paint to paint inside the permanent walls and to the middle of partitions separating rooms	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
19	0	12	status	Status	Status	LB	\N	\N	\N	1	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
19	0	13	remark	Remark	Remark	T	\N	\N	\N	2	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
19	0	14	change_type	change_type	change_type	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	0	15	rental_area	Rental area	Rental area	N	\N	\N	\N	3	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
19	0	16	area_gross	Gross area	Sum of the areas included within the outside face of the exterior walls of a building.	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
19	0	17	area_net	Net area	The wall-to-wall floor area of a room.	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
19	0	18	area_usable	Usable area	generally measured from paint to paint inside the permanent walls and to the middle of partitions separating rooms	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
20	0	13	status	Status	Status	LB	\N	\N	\N	1	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
20	0	14	remark	Remark	Remark	T	\N	\N	\N	2	\N	\N	\N	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
20	0	15	street_id	street_id	street_id	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	16	street_number	street_number	street_number	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	17	tenant_id	tenant_id	tenant_id	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	18	change_type	change_type	change_type	I	\N	\N	\N	\N	\N	4	\N	\N	True	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	0	19	rental_area	Rental area	Rental area	N	\N	\N	\N	4	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
20	0	20	area_gross	Gross area	Sum of the areas included within the outside face of the exterior walls of a building.	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
20	0	21	area_net	Net area	The wall-to-wall floor area of a room.	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
20	0	22	area_usable	Usable area	generally measured from paint to paint inside the permanent walls and to the middle of partitions separating rooms	N	\N	\N	\N	5	\N	20	2	\N	True	\N	\N	1	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: phpgw_cust_attribute_group; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_cust_attribute_group (location_id, id, parent_id, name, group_sort, descr, remark) FROM stdin;
\.


--
-- Data for Name: phpgw_cust_choice; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_cust_choice (location_id, attrib_id, id, value, title, choice_sort) FROM stdin;
43	7	1	Active	\N	0
43	7	2	Banned	\N	0
17	10	1	OK	\N	0
17	10	2	Not OK	\N	0
18	11	1	OK	\N	0
18	11	2	Not OK	\N	0
19	12	1	OK	\N	0
19	12	2	Not OK	\N	0
20	13	1	OK	\N	0
20	13	2	Not OK	\N	0
\.


--
-- Data for Name: phpgw_cust_function; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_cust_function (location_id, id, descr, file_name, active, pre_commit, client_side, custom_sort) FROM stdin;
\.


--
-- Data for Name: phpgw_group_map; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_group_map (group_id, account_id, arights) FROM stdin;
1000	1002	1
1001	1002	1
1003	1004	1
1003	1005	1
1003	1006	1
1003	1007	1
1003	1008	1
\.


--
-- Data for Name: phpgw_history_log; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_history_log (history_id, history_record_id, app_id, history_owner, history_status, history_new_value, history_timestamp, history_old_value, location_id) FROM stdin;
\.


--
-- Data for Name: phpgw_hooks; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_hooks (hook_id, hook_appname, hook_location, hook_filename) FROM stdin;
3	admin	acl_manager	hook_acl_manager.inc.php
4	admin	add_def_pref	hook_add_def_pref.inc.php
5	admin	after_navbar	hook_after_navbar.inc.php
6	admin	config	hook_config.inc.php
7	admin	deleteaccount	hook_deleteaccount.inc.php
8	admin	manual	hook_manual.inc.php
9	admin	view_user	hook_view_user.inc.php
10	admin	menu	admin.menu.get_menu
11	admin	cat_add	admin.cat_hooks.cat_add
12	admin	cat_delete	admin.cat_hooks.cat_delete
13	admin	cat_edit	admin.cat_hooks.cat_edit
14	preferences	deleteaccount	hook_deleteaccount.inc.php
15	preferences	config	hook_config.inc.php
16	preferences	manual	hook_manual.inc.php
17	preferences	settings	hook_settings.inc.php
18	preferences	menu	preferences.menu.get_menu
37	addressbook	add_def_pref	hook_add_def_pref.inc.php
38	addressbook	config_validate	hook_config_validate.inc.php
39	addressbook	home	hook_home.inc.php
40	addressbook	manual	hook_manual.inc.php
41	addressbook	addaccount	hook_addaccount.inc.php
42	addressbook	editaccount	hook_editaccount.inc.php
43	addressbook	deleteaccount	hook_deleteaccount.inc.php
44	addressbook	notifywindow	hook_notifywindow.inc.php
45	addressbook	menu	addressbook.menu.get_menu
46	controller	menu	controller.menu.get_menu
47	controller	config	hook_config.inc.php
48	controller	home	controller.hook_helper.home_backend
49	controller	home_mobilefrontend	controller.hook_helper.home_mobilefrontend
50	controller	settings	hook_settings.inc.php
51	controller	cat_add	controller.cat_hooks.cat_add
52	controller	cat_delete	controller.cat_hooks.cat_delete
53	controller	cat_edit	controller.cat_hooks.cat_edit
54	rental	config	hook_config.inc.php
55	rental	menu	rental.menu.get_menu
56	rental	settings	hook_settings.inc.php
57	frontend	menu	frontend.menu.get_menu
58	frontend	auto_addaccount	frontend.hook_helper.auto_addaccount
59	frontend	config	hook_config.inc.php
60	mobilefrontend	config	hook_config.inc.php
61	mobilefrontend	home	hook_home.inc.php
62	mobilefrontend	set_cookie_domain	mobilefrontend.hook_helper.set_cookie_domain
63	mobilefrontend	set_auth_type	mobilefrontend.hook_helper.set_auth_type
64	mobilefrontend	menu	mobilefrontend.menu.get_menu
83	property	manual	hook_manual.inc.php
84	property	settings	hook_settings.inc.php
85	property	help	hook_help.inc.php
86	property	config	hook_config.inc.php
87	property	menu	property.menu.get_menu
88	property	cat_add	property.cat_hooks.cat_add
89	property	cat_delete	property.cat_hooks.cat_delete
90	property	cat_edit	property.cat_hooks.cat_edit
91	property	home	property.hook_helper.home_backend
92	property	home_mobilefrontend	property.hook_helper.home_mobilefrontend
93	property	addaccount	property.hook_helper.clear_userlist
94	property	editaccount	property.hook_helper.clear_userlist
95	property	deleteaccount	property.hook_helper.clear_userlist
96	property	addgroup	property.hook_helper.clear_userlist
97	property	deletegroup	property.hook_helper.clear_userlist
98	property	editgroup	property.hook_helper.clear_userlist
99	property	registration	property.hook_helper.add_location_contact
100	property	after_navbar	property.hook_helper.after_navbar
101	phpgwapi	menu	phpgwapi.menu_apps.get_menu
102	phpgwapi	login	phpgwapi.menu.clear
\.


--
-- Data for Name: phpgw_interlink; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_interlink (interlink_id, location1_id, location1_item_id, location2_id, location2_item_id, is_private, account_id, entry_date, start_date, end_date) FROM stdin;
\.


--
-- Data for Name: phpgw_interserv; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_interserv (server_id, server_name, server_host, server_url, trust_level, trust_rel, username, password, admin_name, admin_email, server_mode, server_security) FROM stdin;
1	phpGW cvsdemo	\N	http://www.phpgroupware.org/cvsdemo/xmlrpc.php	99	0	\N	\N	\N	\N	xmlrpc	\N
\.


--
-- Data for Name: phpgw_lang; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_lang (message_id, app_name, lang, content) FROM stdin;
new coordinator	property	no	Ny koordinator
transfer budget	property	no	Overfør budsjett
economy	property	no	Økonomi
%1 buildings has been updated to not active of %2 already not active	property	no	%1 Bygninger er oppdatert til IKKE AKTIVE av %2 som var IKKE AKTIVE fra før
%1 entrances has been updated to not active of %2 already not active	property	no	%1 Innganger er oppdatert til IKKE AKTIVE av %2 som var IKKE AKTIVE fra før
%1 entries is added!	property	no	%1 poster er lagt til
%1 entries is updated!	property	no	%1 poster er oppdatert
%1 group	property	no	%1 gruppe
%1 is notified	property	no	Melding sendt til %1
%1 properties has been updated to not active of %2 already not active	property	no	%1 Eiendommer er oppdatert til IKKE AKTIVE av %2 som var IKKE AKTIVE fra før
expenses	property	no	Utgifter
income	property	no	Inntekter
about	common	no	Om
amount in	property	no	Inn
from project	property	no	Fra prosjekt
amount out	property	no	Ut
to project	property	no	Til prosjekt
access error	property	no	Manglende tilgang
accounting	property	no	Regnskap
accounting categories	property	no	Kategorier regnskap
accounting dim b	property	no	Ansvarssted
accounting dim d	property	no	Regnskap dim 6
accounting tax	property	no	Regnskap mva-kode
accounting voucher category	property	no	Regnskap kategorier faktura
accounting voucher type	property	no	Regnskap type faktura
accumulated	property	no	Akkumulert
acl_location is missing	property	no	ACL-lokalisering mangler
acquisition date	property	no	Anskaffelsesdato
action	property	no	Handling
activate tracking of dates in helpdesk main list	property	no	Aktiver visning av datoer for denne entiteten i hovedliste for helpdesk
active	property	no	Aktiv
activities	property	no	Aktiviteter
activity	property	no	Aktivitet
activity code	property	no	Aktivitetskode
activity has been edited	property	no	Aktivitet er rettet
activity has been saved	property	no	Aktivitet er lagret
activity id	property	no	Aktivitet-id
activity num	property	no	Aktivitet num
actor	property	no	Aktør
actual cost	property	no	Betalt
actual_cost	property	no	Betalt
actual cost changed	property	no	Betalt sum er endret
actual cost has been updated	property	no	Betalt sum er oppdatert
actual cost - paid so far	property	no	Betalt sum - pr dd
add a apartment	property	no	Legg til leilighet
add a attrib	property	no	Legg til en attibutt
add a budget account	property	no	Legg til en kostnadsart
add a budget query	property	no	Legg til en budsjettspørring
add a building	property	no	Legg til bygning
add a category	property	no	Legg til en kategori
add a claim	property	no	Legg til et krav
add activity	property	no	Legg til aktivitet
add a custom_function	property	no	Legg til en egendefinert funksjon
add a custom query	property	no	Legg til en egendefinert spørring
add a deviation	property	no	Legg til et avvik
add a document	property	no	Legg til dokument
add a entity	property	no	Legg til en entitet
add a entrance	property	no	Legg til inngang
add a equipment	property	no	Legg til utstyr
add a gab	property	no	Legg til grunneiendom
add agreement	property	no	Legg til avtale
add agreement group	property	no	Legg til en avtalegruppe
add a hour	property	no	Legg til post
add a hour to this template	property	no	lett til en post til denne malen
add a investment	property	no	Legg til investering
add alarm	property	no	Legg til alarm
add alarm for selected user	property	no	Legg til alarm for valgt bruker
add a location	property	no	Legg til en lokalisering
add a meter	property	no	Legg til måler
add a method	property	no	Legg til en metode
add an activity	property	no	Legg til aktivitet
add an actor	property	no	Legg til en aktør
add an agreement	property	no	Legg til en avtale
add an alarm	property	no	Legg til en alarm
add an entity	property	no	Legg til en entitet
add an investment	property	no	Legg til en investering
add an invoice	property	no	Legg til en faktura
add an item to the details	property	no	Legg til en post til detaljer
add another	property	no	Legg til flere
add apartment	property	no	Legg til leilighet
add a part of town	property	no	Legg til en bydel
add a project	property	no	leg til prosjekt
add a property	property	no	Legg til eiendom
add a rental agreement	property	no	Legg til en utleieavtale
add a request	property	no	Legg til behov
add a service agreement	property	no	Legg til en service avtale
add a standard	property	no	Legg til standard
add a template	property	no	Legg til mal
add a ticket	property	no	Legg til melding
add attribute	property	no	Legg til attribute
add a workorder	property	no	Legg til bestilling
add a workorder to this project	property	no	Legg til en bestilling til dette prosjektet
add budget account	property	no	Legg til kostnadsart
add building	property	no	Legg til bygning
add category	property	no	Legg til kategori
add common	property	no	Legg til felles
add custom	property	no	Legg til tilpasset
add custom function	property	no	Legg til egendefinert funksjon
add detail	property	no	Legg til detalj
add deviation	property	no	Legg til avvik
add document	property	no	Legg til dokument
added	property	no	Lagt til
add entity	property	no	Legg til entitet
add entrance	property	no	Legg til inngang
add equipment	property	no	Legg til utstyr
add first value for this prizing	property	no	Legg til første verdi for denne prisingen
add from prizebook	property	no	Legg til fra prisbok
add from template	property	no	Legg til fra mal
add gab	property	no	Legg til grunneiendom
add hour	property	no	Legg til post
add investment	property	no	Legg til investering
add invoice	property	no	Legg til faktura
add items from a predefined template	property	no	Legg til poster fra mal
add items from this vendors prizebook	property	no	Legg til poster fra leverandørs prisbok
additional notes	property	no	Tilleggs kommentarer
add meter	property	no	Legg til måler
add method	property	no	Legg til metode
add new comments	property	no	Legg til ny kommentar
add new ticket	property	no	Legg til ny melding
add project	property	no	Legg til prosjekt
add property	property	no	Legg til prosjekt
add request	property	no	Legg til behov
add request for this project	property	no	Legg til behov til dette prosjektet
add to project as order	property	no	Legg bestilling til prosjekt
add to project as relation	property	no	Kople til prosjekt
addressmasters	common	no	Addressemaster
address	property	no	Adresse
address not defined	property	no	Adresse er ikke definert
adds a new project - then a new workorder	property	no	Legger til nytt prosjekt - så bestilling
adds a new workorder to an existing project	property	no	Legger til ny bestilling til eksisterende prosjekt
add selected request to project	property	no	Legg valgte tiltak til prosjektet
add service	property	no	Legg til service
add single custom line	property	no	Legg til en enkel tilpasset post
add space	property	no	Legg til areal
add standard	property	no	Legg til standard
add status	property	no	Legg til status
adds this workorders calculation as a template for later use	property	no	Legger kalkulasjonen for denne bestillingen som en mal for senere bruk
add template	property	no	Legg til mal
add the selected items	property	no	Legg til valgte element
add this invoice	property	no	Legg til denne fakturaen
add this vendor to this activity	property	no	Legg til denne leverandøren til denne aktiviteten
add ticket	property	no	Legg til melding
add to project	property	no	Legg til eksisterende prosjekt
add workorder	property	no	Legg til bestilling
admin async services	property	no	Administrer planlagte oppgaver
admin custom functions	property	no	Administrer tilpassede funksjoner
admin entity	property	no	Administrer entitet
admin location	property	no	Administrer lokalisering
aesthetics	property	no	Estetikk
again	property	no	igjen
agreement	property	no	Avtale
agreement attributes	property	no	Avtale egenskaper
agreement code	property	no	Avtale kode
agreement group	property	no	Avtale gruppe
agreement group code	property	no	Avtale gruppe kode
agreement group has been edited	property	no	Avtale gruppe er rettet
agreement group has been saved	property	no	Avtale gruppe er lagret
agreement group id	property	no	Avtale gruppe-id
agreement has been edited	property	no	avtale er rettet
agreement has been saved	property	no	avtale er lagret
agreement id	property	no	Avtale-id
agreement_id	property	no	Avtale-id
agreement status	property	no	Avtale status
alarm	property	no	Alarm
alarm id	property	no	Alarm-id
all	property	no	Alle
all users	property	no	Alle brukere
altered	property	no	Endret
altered by	property	no	Endret av
altering columnname or datatype  - deletes your data in this column	property	no	Endring av kolonnenavn eller datatype vil slette data i denne kolonnen
alternative - link instead of uploading a file	property	no	Alternativt - link istedet for opplasting av fil
amount	property	no	Sum
amount not entered!	property	no	Sum ikke angitt
amount of the invoice	property	no	Sum for fakturaen
an unique code for this activity	property	no	En unik kode fo denne aktiviteten
apartment	property	no	Leilighet
apartment has been edited	property	no	Leilighet er rettet
apartment has been saved	property	no	leilighet er lagret
apartment id	property	no	leilighet-id
applications	property	no	Applikasjoner
apply the values	property	no	Bruke verdier
apply	property	no	Mellomlagre
save and stay in form	property	no	Lagre og bli stående i skjemaet
approval	property	no	Godkjenning
approvals	property	no	Godkjenninger
approvals request	property	no	Anmodninger om godkjenning
approval from	property	no	Godkjenning fra
approval from is updated	property	no	Godkjenning fra er oppdatert
approve	property	no	Godkjenn
approve as	property	no	Godkjenn som
approved	property	no	Godkjent
approved amount	property	no	Godkjent beløp
archive	property	no	Arkiv
art	property	no	Art
ask for approval	property	no	Be om godkjenning
assigned from	property	no	Tildelt fra
assignedto	property	no	Tildelt til
assigned to	property	no	Tildelt til
reported by	property	no	Innmeldt av
assign to	property	no	Til
async	property	no	async
asynchronous timed services	property	no	Planlagte oppgaver
async method	property	no	async metode
async method has been saved	property	no	async metode er lagret
async services	property	no	Planlagte oppgaver
at the disposal	property	no	Til disposisjon
attach file	property	no	Legg til som vedlegg
attachments	property	no	Vedlegg
attribute	property	no	Verdier
list entity attribute group	property	no	List egenskapgrupper
attribute group	property	no	Egenskap grupper
attribute has been edited	property	no	Egenskap er rettet
attribute has been saved	property	no	Egenskap er lagret
attribute has not been deleted	property	no	Egenskap er IKKE slettet
attribute has not been edited	property	no	Egenskap er IKKE endret
attribute has not been saved	property	no	Egenskap er IKKE lagret
attribute id	property	no	Egenskap-id
attributes	property	no	Egenskaper
attributes for the attrib	property	no	Verdier for egenskaper
attributes for the entity category	property	no	Egenskaper for entitet-kategori
attributes for the location type	property	no	Egenskaper for lokaliserings type
attributes for the standard	property	no	verdier for standard
a unique code for this activity	property	no	En unik kode fo denne aktiviteten
authorities demands	property	no	Myndighetskrav
auto tax	property	no	Auto MVA
b_account	property	no	Kostnadsart
back to admin	property	no	Tilbake til admin
back to calculation	property	no	Tilbake til kalkulasjon
back to entity	property	no	Returner til entitet
back to investment list	property	no	Tilbake til liste over investeringer
back to list	property	no	Tilbake til liste
back to the list	property	no	Tilbake til liste
back to the ticket list	property	no	Tilbake til meldingsliste uten å lagre
back to the workorder list	property	no	Tilbake til bestillingsliste
base	property	no	Grunnlag
base description	property	no	Beskrivelse av grunnlag
basis	property	no	Basis
besiktigelse	property	no	Besiktigelse
bilagsnr	property	no	Bilagsnr
billable hours	property	no	Egne timer
billable hours changed	property	no	Egne timer endret
billable rate changed	property	no	Fakturerbar sats endret
billable hours has been updated	property	no	Egne timer er oppdatert
bill per unit	property	no	Pris pr enhet, ex mva
buffer	property	no	Buffer
bulk update status	property	no	Masseoppdatering av status
branch	property	no	Fag
b - responsible	property	no	Anviser
bruks nr	property	no	Bruksnr.
budget	property	no	Budsjett
budget account	property	no	Kostnadsart
budget account group	property	no	Kontogruppe
budget account is missing:	property	no	Kostnadsart mangler
budget changed	property	no	Budsjett er endret
budget code is missing from sub invoice in :	property	no	Kostnadsart mangler fra underbilag i :
budget cost	property	no	Budsjett-beløp
budget_cost	property	no	Budsjett-beløp
budget responsible	property	no	Anviser
budsjettsigndato	property	no	Anvist dato
building	property	no	Bygning
building common	property	no	Bygnings felles
building has been edited	property	no	Bygning er endret
building has been saved	property	no	Bygning er lagret
buildingname	property	no	Bygningsnavn
building id	property	no	bygnings-id
building part	property	no	Bygningsdel
building_part	property	no	Bygningsdel
bulk import - contacts	common	no	Masseimport kontakter
bulk import - csv	common	no	Masseimport CSV
but your message could not be sent by mail!	property	no	Men din melding kunne ikke sendes med epost!
calculate	property	no	Kalkulèr
calculate this workorder	property	no	kalkuler bestillingen
calculate workorder	property	no	kalkuler bestillingen
calculate workorder by adding items from vendors prizebook or adding general hours	property	no	kalkuler bestillingen ved å legge til poster fra prisbok - eller egne tilpassede poster
calculation	property	no	Kalkulasjon
cancel the import	property	no	avbryt import
categories	property	no	Kategorier
categories for the entity type	property	no	Kategori for entitet-type
categories for the location type	property	no	Kategori for lokaliseringstype
categorise persons	common	no	Kategoriser personer
category	common	no	Kategori
category changed	property	no	Kategori er endret
category has been edited	property	no	Kategori er rettet
category has been saved	property	no	Kategori er lagret
category has not been saved	property	no	Kategori er IKKE lagret
category id	property	no	Kategori-id
change main screen message	common	no	Endre melding - hovedside
change status	property	no	Endre status
change to	property	no	Endre til
change type	property	no	Endringstype
chapter	property	no	Kapittel
char	property	no	Karakter
character	property	no	karakter
charge tenant	property	no	Fakturer leietaker
check payments	property	no	Sjekk betalinger
check this to have the output to screen before import (recommended)	property	no	Kryss av denne for å kontrollere importdata på skjerm før import (anbefalt)
local files	property	no	Lokale filer
check this to notify your supervisor by email	property	no	Kryss av for å varsle din foresatte med epost
check this to send a mail to your supervisor for approval	property	no	kryss av for å be din foresatte om godkjenning - epost
check to activate custom function	property	no	Merk av for å aktivere egendefinert funksjon
check to add text to order	property	no	Merk for å legge teksten til i bestillingen
check to delete file	property	no	Merk for å slette fil
check to delete this request from this project	property	no	Merk for a slette dette tiltaket fra dette prosjektet
check to inherit from this location	property	no	Merk av for å arve fra denne lokalisering
check to publish text at frontend	property	no	Merk av for å publisere i frontend
check to reset the query	property	no	Merk av for å nulle spørring
check to show this attribue in lookup forms	property	no	Merk av for å vise denne egenskapen i loaliseringsskjema
check to show this attribute in entity list	property	no	Merk av for å vise denne egenskapen i oversikt
check to show this attribute in list	property	no	Merk av for å vise denne egenskapen i oversikt
check to show this attribute in location list	property	no	Merk av for å vise denne egenskapen i oversikt
check to update the email-address for this vendor	property	no	kryss av for å oppdatere adressa til denne leverandøren
check to activate period	property	no	Merk for å aktivere periode
check to close period	property	no	Merk for å avslutte periode
check to delete period	property	no	Merk for å slette periode
child_date	property	no	Dato for referert element
choice	property	no	Valg
choose a category	property	no	Velg en kategori
choose an id	property	no	Velg en-id
choose charge tenant if the tenant i to pay for this project	property	no	Kryss av for å belaste leietaker dersom leietakeren skal betale for arbeidene
choose columns	common	no	Velg kolonner
choose copy hour to copy this hour to a new hour	property	no	Kryss av for å kopiere denne posten til en ny post
choose copy project to copy this project to a new project	property	no	Kryss av for å kopiere dette prosjektet til et nytt prosjekt
choose copy request to copy this request to a new request	property	no	Kryss av for å kopiere dette behovet til et nytt behov
choose copy workorder to copy this workorder to a new workorder	property	no	Kryss av for å kopiere denne bestillingen til en ny bestilling
choose generate id to automaticly assign new id based on type-prefix	property	no	Kryss av for generere ny-id basert på type-prefiks
choose the end date for the next period	property	no	Velg slutt-dato for neste periode
choose the start date for the next period	property	no	Velg start-dato for neste periode
choose to send mailnotification	property	no	Velg å sende påminnelse på epost
chose if this column is nullable	property	no	Velg om denne kolonnen kan være uten verdier
claim	property	no	Krav
claim id	property	no	Krav-id
claim %1 has been saved	property	no	Krav nr %1 er lagret
claim %1 has been edited	property	no	Krav nr %1 er endret
click this button to add a invice	property	no	Klikk på denne knappen for å legg til en faktura
click this button to add a invoice	property	no	Klikk denne knappen for å legge til en faktura
click this button to start the import	property	no	Klikk denne knappen for å starte importen
click this link to edit the period	property	no	klikk denne linken for å rette perioden
click this link to enter the list of sub-invoices	property	no	Klikk her for å entre liste over underbilag
click this link to select	property	no	Klikk her for å velge
click this link to select apartment	property	no	klikk her for å velge leilighet
click this link to select budget account	property	no	klikk her for å velge kostnadsart
click this link to select building	property	no	klikk her for å velge bygning
click this link to select customer	property	no	Klikk her for å velge kunde
click this link to select entrance	property	no	klikk her for å velge inngang
click this link to select equipment	property	no	klikk her for å velge utstyr
click this link to select property	property	no	klikk her for å velge eiendom
click this link to select tenant	property	no	klikk her for å velge leietaker
click this link to select vendor	property	no	klikk her for å velge leverandør
click this link to view the remark	property	no	klikk her for å vise merknad
click this to add an order to an existing project	property	no	klikk her for å legge til en ny bestilling til et eksisterende prosjekt
click this to generate a request with this information	property	no	klikk her for å generere et behov med basis i denne informasjonen
click this to generate a project with this information	property	no	klikk her for å lage et nytt prosjekt med informasjon fra denne saken
click this to link this request to an existing project	property	no	klikk her for å lenke dette behovet til et eksisterende prosjekt
click to view file	property	no	Klikk for vise fil
close	property	no	Avslutt
closed	property	no	Avsluttet
close order	property	no	Avslutt ordre
close this window	property	no	Steng vindu
code	property	no	Kode
collapse all	common	no	Slå sammen
column could not be added	property	no	Kolonne kunne ikke legges til
column description	property	no	Beskrivelse av kolonne
column name	property	no	Kolonnenavn
column name not entered!	property	no	Manglende kolonnenavn
columns	common	no	Kolonner
columns is updated	property	no	Kolonner er oppdatert
common costs	property	no	Fellsekostnader
communication descriptions manager	property	no	Kommunikasjonsbeskrivelse
communication types manager	property	no	Type kommunikasjon
composites	property	no	Leieobjekter
condition	property	no	Tilstand
condition degree	property	no	Tilstandsgrad
condition survey	property	no	Tilstandsanalyse
config	property	no	Konfigurer
configuration	property	no	Konfigurasjon
confirm status	property	no	Bekreft status
delete file	property	no	Slett fil
confirm status to the history	property	no	Bekreft status til historikk
consequence	property	no	Konsekvens
consequence type	property	no	Konsekvenstype
consequential damage	property	no	Følgeskader
consume	property	no	Forbrukt
consume date	property	no	Dato historisk forbruk
consume value	property	no	Beløp historisk forbruk
consume history	property	no	Historisk forbruk
continue	property	no	Fortsett
continuous	property	no	Løpende
contract	property	no	Kontrakt
line	property	no	Linje
sheet	property	no	Ark
choose file	property	no	Velg fil
choose sheet	property	no	Velg ark
choose start line	property	no	Velg startlinje
choose columns	property	no	Velg kolonner
table	property	no	Tabell
condition survey import	property	no	Import av tilstandsanalyse
periodization	property	no	Periodisering
periodization start	property	no	Startperiode
periodization outline	property	no	Periodiseringsfordeling
delete receipt	property	no	Slett kvittering
plan	property	no	Tidsplan
planned year	property	no	Planlagt år
planning	property	no	Planlagt
planning date	property	no	Dato planlagt disponering
planning value	property	no	Beløp planlagt disponering
planning serie	property	no	Planlagt disponering
check to delete	property	no	Merk for å slette
check all	common	no	Merk alle
contact	property	no	Kontakt
contact email	property	no	Epost
contact phone	property	no	Kontakttelefon
contacts	common	no	Kontakter
content	property	no	Innhold
contractual obligations	property	no	Regnskap
contracts	property	no	Kontrakter
contract sum	property	no	Kontraktsum
conversion	property	no	format
coordination	property	no	Koordinering
coordinator	property	no	Koordinator
coordinator changed	property	no	endret koordinator
copy hour ?	property	no	kopier post ?
copy project ?	property	no	kopier prosjekt ?
copy request ?	property	no	kopier behov ?
copy workorder ?	property	no	Kopier bestilling ?
correct error	property	no	Rett opp feil
cost	property	no	kostnad
cost estimate	property	no	Kostnadsestimat
total cost estimate	property	no	Totalt kostnadsestimat
cost - either budget or calculation	property	no	Beløp - enten budsjett eller kalkulasjon
cost (incl tax):	property	no	Kostnader (inkl mva)
cost per unit	property	no	Enhetspris
could not find any location to save to!	property	no	Kunne ikke finne lokalisering å lagre til
count	property	no	Antall
currency	property	no	Valuta
custom	property	no	Tilpasset
customer	property	no	Kunde
custom function	property	no	Egendefinert funksjon
custom function file not chosen!	property	no	Fil for egendefinert funskjon er ikke valgt
custom function has been edited	property	no	Egendefinert funksjon er  rettet
custom function has not been saved	property	no	Egendefinert funksjon er IKKE lagret
custom function id	property	no	Egendefinert funksjons-id
custom functions	property	no	Egendefinert funksjoner
custom functions for the entity category	property	no	Egendefinert funksjoner for entitet-kategori
custom queries	property	no	Egendefinerte spørringer
daily	property	no	Daglig
data	property	no	Data
datatype	property	no	datatype
datatype type not chosen!	property	no	datatype er ikke valgt
date closed	property	no	dato avsluttet
date opened	property	no	Startet
date search	property	no	Datosøk
date from	property	no	Fra dato
date to	property	no	Til Dato
alter date	property	no	Endre dato
datetime	property	no	dato-tid
day	property	no	dag
day of week (0-6, 0=sun)	property	no	Ukedag (0-6, 0=søndag)
days	property	no	Dager
deadline	property	no	Frist
debug	common	no	Debug
debug output in browser	property	no	Kontroller data før import
decimal	property	no	desimal
default	property	no	Standard
default vendor category	property	no	Standard leverandørkategori
default vendor category is updated	property	no	Standard leverandørkategori er oppdatert
delay	property	no	forsinkelse
delete activity	property	no	slett aktivitet
delete agreement and all the activities associated with it!	property	no	slett avtale og alle aktiviteter assosiert med den
delete agreement group and all the activities associated with it!	property	no	Slett avtalegruppe og alle aktiviteter som er assosiert med den
delete apartment	property	no	slett leilighet
delete a single entry by passing the id.	property	no	slett en enkelt post ved å sende id
delete async method	property	no	Slett async-metode
delete budget account	property	no	Slett kostnadsart
delete building	property	no	slett bygning
delete claim	property	no	Slett krav
delete column	property	no	Slett kolonne
delete custom	property	no	Slett egendefinert
delete document	property	no	slett dokument
delete entity	property	no	Slett entitet
delete entity type	property	no	Slett entitet-type
delete entrance	property	no	slett inngang
delete equipment	property	no	slett utstyr
delete failed	property	no	Sletting feilet
delete investment history element	property	no	slett historiepost for investering
delete last entry	property	no	slett siste post
delete last index	property	no	Slett siste index
delete location	property	no	Slett lokalisering
delete location standard	property	no	Slett lokaliseringskonfigurasjons verdi
delete meter	property	no	slett måler
delete owner	property	no	Slett eier
delete part of town	property	no	Slett bydel
delete prize-index	property	no	slett pris-indeks
delete project	property	no	slett prosjekt
delete property	property	no	slett eiendom
delete request	property	no	slett behov
delete template	property	no	slett mal
delete the actor	property	no	Slett aktør
delete the agreement	property	no	Slett avtale
delete the apartment	property	no	slett leilighet
delete the attrib	property	no	slett egenskap
delete the budget account	property	no	Slett kostnadsart
delete the building	property	no	slett bygning
delete the category	property	no	Slett kategori
delete the claim	property	no	Slett krav
delete the custom_function	property	no	Slett egendefinert funskjon
delete the deviation	property	no	Slett avvik
delete the entity	property	no	Slett entitet
delete the entrance	property	no	slett inngang
delete the entry	property	no	slett post
delete the gab	property	no	Slett grunneiendom
delete the item	property	no	Slett post
delete the last index	property	no	Slett siste index
delete the location	property	no	Slett lokalisering
delete the meter	property	no	slett måler
delete the method	property	no	Slett metode
delete the part of town	property	no	Slett bydel
delete the project	property	no	Slett prosjekt
delete the property	property	no	Slett eiendom
delete the r_agreement	property	no	Slett utleieavtale
delete the request	property	no	Slett tiltak
delete the s_agreement	property	no	Slett serviceavtale
delete the standard	property	no	Slett standard
delete the template	property	no	Slett mal
delete the voucher	property	no	Slett bilag
delete this activity	property	no	slett aktivitet
delete this agreement	property	no	slett avtale
delete this agreement group	property	no	Slett denne avtalegruppen
delete this column from the output	property	no	Fjern denne kolonnen fra visning
delete this document	property	no	slett dokument
delete this entity	property	no	Slett denne entiteten
delete this entry	property	no	slett post
delete this equipment	property	no	slett utstyr
delete this hour	property	no	slett post
delete this item	property	no	Slett denne posten
delete this project	property	no	slett prosjekt
delete this request	property	no	slett behov
delete this value from the list of multiple choice	property	no	Slett denne verdien fra listen over fler-valg
delete this vendor from this activity	property	no	slett denne leverandøren fra denne aktiviteten
delete this workorder	property	no	slett bestilling
delete ticket	property	no	slett melding
delete value	property	no	Slett verdi
delete vendor activity	property	no	Slett aktivitet
delete voucher	property	no	Slett bilag
delete workorder	property	no	Slett bestilling
delivered	property	no	Levert
delivery address	property	no	Leveringsadresse
department	common	no	Avdeling
deposit claim	property	no	Krav mot depositum
deposition	property	no	Avsetning
description	property	no	Beskrivelse
description order	property	no	Bestilling til leverandør
descr	property	no	Beskrivelse
details	property	no	Detaljer
deviation	property	no	Avvik
deviation has been added	property	no	Avvik er lagt til
deviation has been edited	property	no	Avvik er rettet
deviation id	property	no	Avvik-id
difference	property	no	Rest
dim a	property	no	Dim A
dim a is missing	property	no	Dim A mangler
dima is missing from sub invoice in:	property	no	Dim A mangler fra underbilag i :
dim b	property	no	Ansvarssted
dimb	property	no	Ansvarssted
dimb roles	property	no	Roller ansvarssted
dimb role user	property	no	Bruker/rolle/ansvarssted
no authorities demands	property	no	Ingen myndighetskrav
no dimb	property	no	Ansvarssted ikke valgt
please select dimb!	property	no	Angi ansvarssted
dim d	property	no	Dim 6
dime	property	no	Kategori
directory created	property	no	katalog er opprettet
disable	property	no	Deaktiver
disabled	property	no	inaktivt
district	property	no	Område
district_id	property	no	Distrikt-id
doc type	property	no	Dokumenttype
document	property	no	Dokument
document %1 has been edited	property	no	Dokument %1 er rettet
document %1 has been saved	property	no	Dokument %1 er lagret
documentation	property	no	Dokumentasjon
documentation for locations	property	no	Dokumentasjon for arealer
document categories	property	no	Dokumentkategorier
document date	property	no	Dokumentdato
document id	property	no	Dokument id
document name	property	no	Dokumentnavn
documents	property	no	Dokumenter
document status	property	no	Dokumentstatus
domain name for mail-address, eg. %1	property	no	Domenenavn for e-post adresser
do not add this invoice	property	no	Ikke legg til denne fakturaen
do not import this invoice	property	no	Ikke importer dette bilaget
down	property	no	Ned
download	common	no	Last ned
download table to your browser	common	no	Last ned tabell til din nettleser
do you really want to change the status to %1	property	no	Vil du virkelig endre status til %1
do you really want to change the priority to %1	property	no	Vil du virkelig endre prioritet til %1
do you really want to delete this entry	property	no	Vil du virkelig slette denne posten
do you really want to update the categories	property	no	vil du virkelig oppdatere kategoriene
do you really want to update the categories again	property	no	vil du virkelig oppdatere kategoriene igjen
do you want to perform this action	property	no	Vil du gjennomføre denne handlingen
draft	property	no	UTKAST
economy and progress	property	no	Økonomi og fremdrift
edit activity	property	no	Endre aktivitet
edit agreement	property	no	Endre avtale
edit agreement group	property	no	Rette avtale gruppe
edit apartment	property	no	Endre leilighet
edit attribute	property	no	Endre egenskap
edit budget account	property	no	Rette kostnadsart
edit building	property	no	Endre bygning
edit categories	property	no	Endre kategorier
edit category	property	no	Endre kategori
edit claim	property	no	Endre klage
edit custom fields	property	no	Endre egendefinerte felt
edit custom function	property	no	Rette egendefinert funksjon
edit/customise this hour	property	no	Endre eller tilpass denne posten
edit deviation	property	no	Endre avvik
edit document	property	no	Endre dokument
edit entity	property	no	Endre entitet
edit entrance	property	no	Endre inngang
edit equipment	property	no	Endre utstyr
edit gab	property	no	Endre grunneiendom
edit hour	property	no	Endre post
edit id	property	no	Endre-id
edit info	property	no	Endre informasjon
edit information about the document	property	no	Endre informasjon om dokumentet
edit location config for	property	no	Endre konfigurasjon for lokalisering
edit log levels	common	no	Endre log nivåer
edit meter	property	no	Endre måler
edit method	property	no	Endre metode
edit period	property	no	Endre periode
edit pricing	property	no	Endre prising
edit priority key	property	no	Endre prioriteringsnøkkel
edit project	property	no	Endre prosjekt
edit property	property	no	Endre eiendom
edit request	property	no	Endre behov
edit standard	property	no	Endre standard
edit status	property	no	Endre status
edit template	property	no	Endre mal
edit the account	property	no	Endre kontoen
edit the actor	property	no	Endre aktør
edit the agreement	property	no	Endre avtale
edit the agreement_group	property	no	Endre avtalegruppe
edit the alarm	property	no	Endre alarm
edit the apartment	property	no	Endre leilighet
edit the attrib	property	no	Endre egenskap
edit the budget account	property	no	Endre kostnadsart
edit the building	property	no	Endre bygning
edit the category	property	no	Endre kategorien
edit the claim	property	no	Endre kravet
edit the column relation	property	no	Endre kolonnerelasjonen
edit the custom_function	property	no	Endre egendefinert funksjon
edit the deviation	property	no	Endre avvik
edit the entity	property	no	Endre entitet
edit the entrance	property	no	Endre inngang
edit the equipment	property	no	Endre utstyr
edit the gab	property	no	Endre grunneiendom
edit the location	property	no	Endre lokalisering
edit the meter	property	no	Endre måler
edit the method	property	no	Endre metoden
edit the part of town	property	no	Endre bydelen
edit the pricebook	property	no	Endre prisbok
edit the project	property	no	Endre prosjekt
edit the property	property	no	Endre eiendom
edit the r_agreement	property	no	Endre utleieavtalen
edit the request	property	no	Endre behov
edit the s_agreement	property	no	Endre serviceavtalen
edit the standard	property	no	Endre standard
edit the template	property	no	Endre mal
edit the workorder	property	no	Endre bestilling
edit this activity	property	no	Endre aktivitet
edit this entity	property	no	Endre entiteten
edit this entry	property	no	Endre post
edit this entry equipment	property	no	Endre utstyr
edit this entry project	property	no	Endre prosjekt
edit this entry request	property	no	Endre behov
edit this entry workorder	property	no	Endre bestilling
edit this meter	property	no	Endre måler
edit workorder	property	no	Endre bestilling
email-address of the user, eg. %1	property	no	E-post adresse for bruker, %1
enable	property	no	Aktiver
enabled	property	no	Aktiv
enable file upload	property	no	Aktiver opplasting av filer
enable history for this attribute	property	no	Aktiver historikk for denne egenskapen
enable link from location detail	property	no	Aktiver link fra detaljer for areal
list vendors per activity	property	no	List leverandør pr aktivitet
enables help message for this attribute	property	no	Hjelpetekst for denne egenskapen
enable start project from this category	property	no	Aktiviser mulighet for å starte prosjekt herfra
end	property	no	Slutt
end date	property	no	Sluttdato
enter additional remarks to the description - if any	property	no	gi en merknad - om noen
enter a descr for the custom function	property	no	Angi en beskrivelse av egendefinert funksjon
enter a description for prerequisitions for this activity - if any	property	no	gi en beskrivelse av nødvendige grunnlagsarbeid - om noen - for denne aktiviteten
enter a description of the deviation	property	no	Angi en beskrivelse av avviket
enter a description of the document	property	no	Gi en beskrivelse av dokumentet
enter a description of the equipment	property	no	Gi en beskrivelse av utstyret
enter a description of the project	property	no	Gi en beskrivelse av prosjektet
enter a description of the request	property	no	Gi en beskrivelse av tilstanden
enter a description of the standard	property	no	Gi en beskrivelse av standarden
enter a description of the status	property	no	Angi en beskrivelse av statusen
enter a description the attribute	property	no	Gi en beskrivelse av egenskapen
enter a description the budget account	property	no	Angi en beskrivelse av kostnadsarten
enter a description the category	property	no	Angi en beskrivelse av kategorien
enter a description the method	property	no	Angi en beskrivelse av metoden
enter a description the standard	property	no	Gi en beskrivelse av  standarden
enter a meter id !	property	no	Angi måler-id
enter a name for the query	property	no	Angi et navn for spørringen
enter a name for this part of town	property	no	Angi ett navn for denne bydelen
enter a name of the standard	property	no	gi standarden et navn
enter a new grouping for this activity if not found in the list	property	no	skriv inn en ny gruppering for denne posten om den ikke finnes i listen
enter a new index	property	no	Gi en ny index
enter a new writeoff period if it is not in the list	property	no	Skriv inn en ny avskrivningsperiode om den ikke finnes i listen
enter any persentage addition per unit	property	no	Angi prosentvis tillegg pr enhet
enter any remarks regarding this apartment	property	no	Angi merknader vedrørende denne leiligheten
enter any remarks regarding this building	property	no	Angi merknader vedrørende denne bygningen
enter any remarks regarding this entrance	property	no	Angi merknader vedrørende denne inngangen
enter any round sum addition per order	property	no	Angi riggtillegg
enter apartment id	property	no	Angi leilighets-id
enter a remark for this claim	property	no	Angi en merknad for dette kravet
enter a remark for this entity	property	no	Angi en merknad for denne entiteten
enter a remark - if any	property	no	gi en merknad - om noen
enter a remark to add to the history of the project	property	no	Legg til en merknad til historikken for prosjektet
enter a short description of the workorder	property	no	Gi en kort beskrivelse av bestillingen
enter a short description of this template	property	no	Gi en kort beskrivelse av malen
enter a sql query	property	no	Skriv inn en sql-spørring
enter a standard prefix for the id	property	no	Angi et prefix for-id
enter a standard prefix for the id of the equipments	property	no	Angi et standard prefix for utstyr av denne typen
enter a statustext for the inputfield in forms	property	no	Angi en statustekst for inputfeltet
enter a value for the labour cost	property	no	Angi arbeids kostnader
enter a value for the material cost	property	no	Angi materialkostnader
enter actual cost	property	no	Angi betalt sum
enter building id	property	no	Angi bygnings-id
enter document name	property	no	Angi dokument-id
enter document title	property	no	Angi dokument tittel
enter document version	property	no	Angi dokument versjon
enter entrance id	property	no	Angi inngangs-id
enter equipment id	property	no	Angi utstyrs-id
enter invoice number	property	no	Angi bilagsnummer
enter kid nr	property	no	Angi KID nr
enter other branch if not found in the list	property	no	Angi andre fag, om det ikke finnes i listen
enter project name	property	no	Angi prosjektnavn
enter quantity of unit	property	no	Angi mengde
enter request title	property	no	Angi beskrivende navn for tiltak
enter the attribute id	property	no	Angi egenskap-id
enter the attribute value for this entity	property	no	Angi egenskap verdi for denne posten
enter the billable hour for the task	property	no	Angi egne timer for oppgaven
enter the budget	property	no	Angi budsjett
enter the budget account	property	no	Angi kostnadsart
enter the category id	property	no	Angi kategori-id
enter the cost per unit	property	no	Angi kostnad pr enhet
enter the date for this reading	property	no	Angi dato for denne avlesningen
enter the default value	property	no	Angi standard-verdi
enter the description	property	no	Angi beskrivelse
enter the description for this activity	property	no	Angi beskrivelse av denne aktiviteten
enter the description for this template	property	no	Angi beskrivelse av denne malen
enter the details of this ticket	property	no	Angi detaljer for meldingen
enter the email-address for this user	property	no	Oppgi epostadressen for denne brukeren
enter the expected longevity in years	property	no	Angi forventet levetid [år]
enter the floor	property	no	Angi etasje
enter the floor id	property	no	Angi etasje-id
enter the general address	property	no	Angi generell adresse
enter the input text for records	property	no	Angi ledetekst for datafelt
enter the invoice date	property	no	Angi fakturadato
enter the meter id	property	no	Angi måler-id
enter the method id	property	no	Angi metode-id
enter the name for the column	property	no	Angi navn for kolonne i database
enter the name for this location	property	no	Angi navn for denne lokaliseringen
enter the name of the apartment	property	no	Angi navn for leiligheten
enter the name of the building	property	no	Angi navn for bygningen
enter the name of the entrance	property	no	Angi navn for inngangen
enter the name of the meter	property	no	Angi navn for måleren
enter the name of the property	property	no	Angi navn for eiendommen
enter the name the template	property	no	Angi navn for malen
enter the payment date or the payment delay	property	no	Angi betalingsdato eller antall dager til forfall
enter the power meter	property	no	Legg til måleren
enter the power_meter	property	no	Angi måler
enter the property id	property	no	Angi eiendoms-id
enter the purchase cost	property	no	Angi anskaffelses kostnad
enter the record length	property	no	Angi lengde for datafelt
enter the reserve	property	no	Angi reserve
enter the scale if type is decimal	property	no	Angi skala dersom type er desimal
enter the search string. to show all entries, empty this field and press the submit button again	property	no	Angi søkestrengen. for å vise alle poster, tøm dette feltet og søk på nytt
enter the standard id	property	no	Angi standard-id
enter the status id	property	no	Angi statius-id
enter the street number	property	no	Angi gatenummer
enter the subject of this ticket	property	no	Angi overskrift for denne meldingen
enter the total cost of this activity - if not to be calculated from unit-cost	property	no	Angi total kostnad for denne posten - dersom den ikke skal beregnes fra enhetskostnader
enter the workorder id to search by workorder - at any date	property	no	Angi bestillings-id for å søke etter bestilling - gjelder alle datoer
enter workorder title	property	no	Angi bestillings tittel
entity	property	no	type
entity has been added	property	no	Post er lagt til
entity has been edited	property	no	Post er oppdatert
entity %1 has been edited	property	no	Post %1 er oppdatert
entity has not been edited	property	no	Post er IKKE oppdatert
entity has not been saved	property	no	Post er IKKE lagret
entity id	property	no	entitets-id
entity name	property	no	entitetsnavn
entity not chosen	property	no	Entitet er ikke valgt
entity num	property	no	Entitet num
entity type	property	no	entitetstype
entity type not chosen!	property	no	Entitet type er ikke valgt
entrance	property	no	inngang
entrance has been edited	property	no	Inngang er rettet
entrance has been saved	property	no	Inngang er lagret
entrance id	property	no	Inngangs-id
entry date	common	no	Registrert dato
entry_date	common	no	Registrert dato
equipment	property	no	Utstyr
equipment %1 has been edited	property	no	Utstyr %1 er rettet
equipment %1 has been saved	property	no	utstyr %1 er lagret
equipment id	property	no	utstyrs-id
equipment_id	property	no	utstyrs-id
equipment type	property	no	Type utstyr/anlegg
estimate	property	no	Kostnadsestimat
event	property	no	Hendelse
event action	property	no	Handling ved hendelse
events	property	no	Hendelser
example	property	no	Eksempel
exception	property	no	Utgår
expand all	common	no	Vis alle
exp date	property	no	Utgått dato
export contacts	property	no	Eksporter kontakter
export date	property	no	Eksport dato
export invoice	property	no	Overfør
export to file	property	no	Eksport til fil
external ref	property	no	Ekstern referanse
extra	property	no	Ekstra
extra mail address	property	no	Ekstra adresse
f	property	no	F
failed to copy file !	property	no	Kopiering av fil feilet!
failed to create directory	property	no	Klarte ikke å lage katalog
failed to delete file	property	no	Klarte ikke å slette fil
failed to upload file !	property	no	Opplasting av fil feilet!
failure	property	no	Svikt
false	property	no	False
feste nr	property	no	Festenr.
fetch the history for this item	property	no	Hent historikk for dette elementet
file	property	no	fil
file deleted	property	no	Fil er slettet
filename	property	no	Filnavn
fileuploader	property	no	Filopplasting
find and register all application hooks	property	no	Finn og registrer alle Hooks
finnish date	property	no	Ferdig dato
finnish_date	property	no	Ferdig dato
finnish date changed	property	no	Endret ferdig-dato
first entry is added!	property	no	Fil er lagt til
firstname	property	no	Fornavn
first name of the user, eg. %1	property	no	Fornavn til bruker, %1
first note added	property	no	Første notat er lagt til
float	property	no	Flyttall
floor	property	no	Etasje
floor common	property	no	Etasje felles
floor id	property	no	Etasje-id
fm_vendor	property	no	Leverandør
force year for period	property	no	Overstyr år for periode
format type	property	no	Format type
formats	property	no	Formater
forward	property	no	Videresend
fraction	property	no	Fraksjon
fraction::dividend	property	no	Brøk::teller
fraction::divisor	property	no	Brøk::nevner
from	property	no	Fra
from date	property	no	Fra dato
funding	property	no	Finansiering
gaards nr	property	no	Gårdsnr.
gab	property	no	Grunneiendom
gabnr	property	no	Grunneiendom
gallery	property	no	Bildearkiv
general	property	no	Generelt
generic	property	no	Generelt
general address	property	no	Generell adresse
general info	property	no	Generell informasjon
generate a project from this request	property	no	Generer et prosjekt basert på dette behovet
generate id ?	property	no	generer id
generate order	property	no	Lag bestilling
generate project	property	no	Lag nytt prosjekt
generate request	property	no	Registrer behov
generate new project	property	no	Registrer nytt prosjekt
get list	property	no	Hent liste
global	property	no	Global
global categories	property	no	Globale kategorier
global configuration	property	no	Global konfigurasjon
grant access	property	no	Gi tilgang
group	property	no	Gruppe
grouping	property	no	Gruppering
helpdesk	property	no	Meldinger
help message	property	no	Hjelpetekst
highest	property	no	Høyest
history	property	no	Historikk
history not allowed for this datatype	property	no	Historikk er ikke tilgjengelig for denne datatypen
history of this attribute	property	no	Historie for denne egenskapen
hits	property	no	Treff
hour	property	no	Time
hour %1 has been deleted	property	no	Post %1 er slettet
hour %1 has been edited	property	no	Post %1 er rettet
hour %1 is added!	property	no	Post %1 er lagt til
hour category	property	no	Post-kategori
hour id	property	no	Post-id
html	property	no	HTML
id	property	no	ID
id control	property	no	ID-kontroll
id is updated	property	no	ID er oppdatert
id not entered!	property	no	ID er ikke valgt
if files can be uploaded for this category	property	no	Om filer skal kunne lastes opp for denne kategorien
if this entity type is to be linked to a location	property	no	Om denne entitetstypen skal linkes til en lokalisering
if this entity type is to be linked to documents	property	no	Om denne entitetstypen skal linkes til dokumentasjon
if this entity type is to look up tenants	property	no	Om denne entiteten skal gjøre oppslag på leietakere
import calculation	property	no	Importer kalkulasjon
import	property	no	Import
importance	property	no	Viktighet
import detail	property	no	Importer detalj
import details to this agreement from spreadsheet	property	no	Import detaljer til denne avtalen fra regneark
import from csv	property	no	Fil-import til Faktura
import invoice	property	no	Import faktura
import this invoice	property	no	Importer denne fakturaen
import vcard	common	no	Importer visittkort
incl tax	property	no	inkl mva
include in location form	property	no	Inkluder i lokaliseringsskjema
include in search	property	no	Inkluder i søk
include the workorder to this claim	property	no	Inkluder bestillingen i dette kravet
include this entity	property	no	Inkluder denne entiteten
index	property	no	Indeks
index count	property	no	Antall
index_count	property	no	Indeks teller
index date	property	no	Dato for indeks
indoor climate	property	no	Innendørsklima
initial category	property	no	Initiell kategori
initial coordinator	property	no	Initiell koordinator
initials	property	no	Initialer
guam	common	en	GUAM
initial status	property	no	Initiell status
initial value	property	no	Initiell verdi
in progress	property	no	Påbegynt
inputdata for the method	property	no	Inputdata for metoden
input data for the nethod	property	no	Input data til metoden
input text	property	no	ledetekst
input text not entered!	property	no	Input tekst er ikke angitt!
input type	property	no	Input-type
input name	property	no	Input-navn
invalid category	property	no	Ugyldig kategori
select value	property	no	Angi verdi
selected mail addresses	property	no	Valgte adresser
insert the date for the acquisition	property	no	Angi dato for anskaffelsen
insert the date for the initial value	property	no	Angi dato for initiell verdi
insert the value at the start-date as a positive amount	property	no	Angi verdie ved startdatoen som en positiv verdi
integer	property	no	Heltall
interval	property	no	Intervall
inventory	common	no	Beholdning
add inventory	common	no	Legg til beholdning
investment	property	no	Investering
investment added !	property	no	Investering er lagt til
investment history	property	no	Investerings historie
investment id	property	no	Investerings-id
investment id:	property	no	Investerings-id:
investment value	property	no	Avskrivning
invoice	property	no	Faktura
invoice address	property	no	Fakturaadresse
invoice date	property	no	Fakturadato
invoice id	property	no	Faktura-id
invoice id already taken for this vendor	property	no	Fakturanummeret er allerede registrert for denne leverandøren
invoice is not added!	property	no	Fakturaen er IKKE lagt til!
invoice number	property	no	Fakturanr.
invoice transferred	property	no	Bilag er overført
invoice line text	property	no	Posteringstekst
is id	property	no	Er id
is registered	property	no	er registert
list voucher	property	no	list underbilag
is there a demand from the authorities to correct this condition?	property	no	Finnes det myndighetskrav for å rette opp tilstanden?
items	property	no	Detaljer
janitor	property	no	Bestiller
new janitor	property	no	Ny bestiller
jasper reports	property	no	JasperReports
jasper upload	property	no	Opplasting av JasperReports
key deliver location	property	no	Sted for å levere nøkkel
key fetch location	property	no	Sted for å hente nøkkel
key location	property	no	Nøkkel lokalisert
key responsible	property	no	Nøkkel ansvarlig
kid nr	property	no	KID nr
kommune nr	property	no	Kommunenr.
kreditnota	property	no	Kreditnota
labour cost	property	no	Arbeid
large	property	no	Stor
last index	property	no	Siste indeks
lastname	property	no	Etternavn
last name of the user, eg. %1	property	no	Etternavn til bruker, %1
laws and regulations	property	no	Lover og forskrifter
leave the actor untouched and return back to the list	property	no	Forlat aktøren uendret og returner til oversikten
leave the agreement untouched and return back to the list	property	no	Forlat avtalen uendret og returner til oversikten
about	common	en	About
access	common	en	Access
access not permitted	common	en	Access not permitted
account has been created	common	en	Account has been created
account has been deleted	common	en	Account has been deleted
account has been updated	common	en	Account has been updated
acl	common	en	ACL
action	common	en	Action
active	common	en	Active
add	common	en	Add
add %1 category for	common	en	Add %1 category for
add category	common	en	Add category
add sub	common	en	Add sub
address book	common	en	Address book
addressbook	common	en	Addressbook
admin	common	en	Admin
administration	common	en	Administration
afghanistan	common	en	AFGHANISTAN
albania	common	en	ALBANIA
algeria	common	en	ALGERIA
all	common	en	All
american samoa	common	en	AMERICAN SAMOA
andorra	common	en	ANDORRA
angola	common	en	ANGOLA
anguilla	common	en	ANGUILLA
antarctica	common	en	ANTARCTICA
antigua and barbuda	common	en	ANTIGUA AND BARBUDA
applications	common	en	applications
apply	common	en	Apply
april	common	en	April
are you sure you want to delete this entry ?	common	en	Are you sure you want to delete this entry ?
argentina	common	en	ARGENTINA
armenia	common	en	ARMENIA
aruba	common	en	ARUBA
august	common	en	August
australia	common	en	AUSTRALIA
austria	common	en	AUSTRIA
author	common	en	Author
autosave default category	common	en	Autosave Default Category
azerbaijan	common	en	AZERBAIJAN
back	common	en	Back
bad login or password	common	en	Bad login or password
bahamas	common	en	BAHAMAS
bahrain	common	en	BAHRAIN
bangladesh	common	en	BANGLADESH
barbados	common	en	BARBADOS
belarus	common	en	BELARUS
belgium	common	en	BELGIUM
belize	common	en	BELIZE
benin	common	en	BENIN
bermuda	common	en	BERMUDA
bhutan	common	en	BHUTAN
blocked, too many attempts	common	en	Blocked, too many attempts
bolivia	common	en	BOLIVIA
bosnia and herzegovina	common	en	BOSNIA AND HERZEGOVINA
botswana	common	en	BOTSWANA
bouvet island	common	en	BOUVET ISLAND
brazil	common	en	BRAZIL
british indian ocean territory	common	en	BRITISH INDIAN OCEAN TERRITORY
brunei darussalam	common	en	BRUNEI DARUSSALAM
bulgaria	common	en	BULGARIA
burkina faso	common	en	BURKINA FASO
burundi	common	en	BURUNDI
calendar	common	en	Calendar
cambodia	common	en	CAMBODIA
cameroon	common	en	CAMEROON
canada	common	en	CANADA
cancel	common	en	Cancel
cape verde	common	en	CAPE VERDE
categories	common	en	Categories
categories for	common	en	categories for
category	common	en	Category
category %1 has been added !	common	en	Category %1 has been added !
category %1 has been updated !	common	en	Category %1 has been updated !
cayman islands	common	en	CAYMAN ISLANDS
guatemala	common	en	GUATEMALA
leave the claim untouched and return back to the list	property	no	Forlat kravet uendret og returner til oversikten
leave the custom untouched and return back to the list	property	no	Forlat egendefinert uendret og returner til oversikten
leave the owner untouched and return back to the list	property	no	Forlat eier uendret og returner til oversikten
leave the part of town untouched and return back to the list	property	no	Forlat bydel uendret og returner til oversikten
leave the rental agreement untouched and return back to the list	property	no	Forlat leieavtalen uendret og returner til oversikten
leave the service agreement untouched and return back to the list	property	no	Forlat serviceavtalen uendret og returner til oversikten
let this entity show up in location form	property	no	Vis denne entiteten i lokaliseringsskjemaet
link	property	no	Lenke
link from location	property	no	Lenke fra lokalisering
link to the origin for this request	property	no	Link til opprinnelsen for dette tiltaket
list workorder	property	no	List bestillinger
central african republic	common	en	CENTRAL AFRICAN REPUBLIC
chad	common	en	CHAD
change	common	en	Change
charset	common	en	utf-8
chile	common	en	CHILE
china	common	en	CHINA
choose the category	common	en	Choose the category
choose the parent category	common	en	Choose the parent category
christmas island	common	en	CHRISTMAS ISLAND
clear	common	en	Clear
clear form	common	en	Clear Form
close	common	en	Close
cocos (keeling) islands	common	en	COCOS (KEELING) ISLANDS
colombia	common	en	COLOMBIA
comoros	common	en	COMOROS
congo	common	en	CONGO
congo, the democratic republic of the	common	en	CONGO, THE DEMOCRATIC REPUBLIC OF THE
cook islands	common	en	COOK ISLANDS
copy	common	en	Copy
costa rica	common	en	COSTA RICA
cote d ivoire	common	en	COTE D IVOIRE
create	common	en	Create
created by	common	en	Created By
croatia	common	en	CROATIA
cuba	common	en	CUBA
currency	common	en	Currency
current	common	en	Current
current users	common	en	Current users
cyprus	common	en	CYPRUS
czech republic	common	en	CZECH REPUBLIC
date	common	en	Date
date due	common	en	Date Due
december	common	en	December
default category	common	en	Default Category
delete	common	en	Delete
denmark	common	en	DENMARK
description	common	en	Description
detail	common	en	Detail
details	common	en	Details
disabled	common	en	Disabled
display monday first	common	en	Display Monday first
display sunday first	common	en	Display Sunday first
djibouti	common	en	DJIBOUTI
do you also want to delete all subcategories ?	common	en	Do you also want to delete all subcategories ?
domain	common	en	Domain
domain name for mail-address, eg. %1	common	en	domain name for mail-address, eg. %1
domestic	common	en	Domestic
dominica	common	en	DOMINICA
dominican republic	common	en	DOMINICAN REPUBLIC
done	common	en	Done
drag to move	common	en	Drag to move
e-mail	common	en	E-Mail
east timor	common	en	EAST TIMOR
ecuador	common	en	ECUADOR
edit	common	en	Edit
edit %1 category for	common	en	Edit %1 category for
edit categories	common	en	Edit Categories
edit category	common	en	Edit category
egypt	common	en	EGYPT
el salvador	common	en	EL SALVADOR
email	common	en	E-Mail
email-address of the user, eg. %1	common	en	email-address of the user, eg. %1
enabled	common	en	Enabled
end date	common	en	End date
end time	common	en	End time
entry has been deleted sucessfully	common	en	Entry has been deleted sucessfully
entry updated sucessfully	common	en	Entry updated sucessfully
equatorial guinea	common	en	EQUATORIAL GUINEA
eritrea	common	en	ERITREA
error	common	en	Error
error creating %1 %2 directory	common	en	Error creating %1 %2 directory
error deleting %1 %2 directory	common	en	Error deleting %1 %2 directory
error renaming %1 %2 directory	common	en	Error renaming %1 %2 directory
estonia	common	en	ESTONIA
ethiopia	common	en	ETHIOPIA
falkland islands (malvinas)	common	en	FALKLAND ISLANDS (MALVINAS)
faroe islands	common	en	FAROE ISLANDS
fax number	common	en	fax number
february	common	en	February
fields	common	en	Fields
fiji	common	en	FIJI
files	common	en	Files
filter	common	en	Filter
finland	common	en	FINLAND
first name	common	en	First name
first name of the user, eg. %1	common	en	first name of the user, eg. %1
first page	common	en	First page
firstname	common	en	Firstname
fixme!	common	en	FIXME!
force selectbox	common	en	Force SelectBox
france	common	en	FRANCE
french guiana	common	en	FRENCH GUIANA
french polynesia	common	en	FRENCH POLYNESIA
french southern territories	common	en	FRENCH SOUTHERN TERRITORIES
friday	common	en	Friday
ftp	common	en	FTP
fullname	common	en	Fullname
gabon	common	en	GABON
gambia	common	en	GAMBIA
general menu	common	en	General Menu
georgia	common	en	GEORGIA
germany	common	en	GERMANY
ghana	common	en	GHANA
gibraltar	common	en	GIBRALTAR
global	common	en	Global
global public	common	en	Global Public
go today	common	en	Go Today
grant access	common	en	Grant access
greece	common	en	GREECE
greenland	common	en	GREENLAND
grenada	common	en	GRENADA
group	common	en	Group
group access	common	en	Group Access
group has been added	common	en	Group has been added
group has been deleted	common	en	Group has been deleted
group has been updated	common	en	Group has been updated
group name	common	en	group name
group public	common	en	Group Public
groups	common	en	Groups
groups with permission for %1	common	en	Groups with permission for %1
groups without permission for %1	common	en	Groups without permission for %1
guadeloupe	common	en	GUADELOUPE
guinea	common	en	GUINEA
guinea-bissau	common	en	GUINEA-BISSAU
guyana	common	en	GUYANA
haiti	common	en	HAITI
heard island and mcdonald islands	common	en	HEARD ISLAND AND MCDONALD ISLANDS
help	common	en	Help
high	common	en	High
highest	common	en	Highest
holy see (vatican city state)	common	en	HOLY SEE (VATICAN CITY STATE)
home	common	en	Home
honduras	common	en	HONDURAS
hong kong	common	en	HONG KONG
hungary	common	en	HUNGARY
iceland	common	en	ICELAND
india	common	en	INDIA
indonesia	common	en	INDONESIA
international	common	en	International
invalid ip address	common	en	Invalid IP address
invalid password	common	en	Invalid password
iran, islamic republic of	common	en	IRAN, ISLAMIC REPUBLIC OF
iraq	common	en	IRAQ
ireland	common	en	IRELAND
israel	common	en	ISRAEL
it has been more than %1 days since you changed your password	common	en	It has been more than %1 days since you changed your password
it is recommended that you run setup to upgrade your tables to the current version	common	en	It is recommended that you run setup to upgrade your tables to the current version.
italy	common	en	ITALY
jamaica	common	en	JAMAICA
january	common	en	January
japan	common	en	JAPAN
jordan	common	en	JORDAN
july	common	en	July
june	common	en	June
kazakstan	common	en	KAZAKSTAN
kenya	common	en	KENYA
keywords	common	en	Keywords
kiribati	common	en	KIRIBATI
korea, democratic peoples republic of	common	en	KOREA, DEMOCRATIC PEOPLES REPUBLIC OF
korea, republic of	common	en	KOREA, REPUBLIC OF
kuwait	common	en	KUWAIT
kyrgyzstan	common	en	KYRGYZSTAN
language	common	en	Language
lao peoples democratic republic	common	en	LAO PEOPLES DEMOCRATIC REPUBLIC
last name	common	en	Last name
last name of the user, eg. %1	common	en	last name of the user, eg. %1
last page	common	en	Last page
lastname	common	en	Lastname
latvia	common	en	LATVIA
lebanon	common	en	LEBANON
lesotho	common	en	LESOTHO
liberia	common	en	LIBERIA
libyan arab jamahiriya	common	en	LIBYAN ARAB JAMAHIRIYA
liechtenstein	common	en	LIECHTENSTEIN
list	common	en	List
lithuania	common	en	LITHUANIA
local	common	en	Local
login	common	en	Login
loginid	common	en	LoginID
logout	common	en	Logout
low	common	en	Low
lowest	common	en	Lowest
luxembourg	common	en	LUXEMBOURG
macau	common	en	MACAU
macedonia, the former yugoslav republic of	common	en	MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF
madagascar	common	en	MADAGASCAR
mail domain, eg. %1	common	en	mail domain, eg. %1
main category	common	en	Main category
main screen	common	en	Main screen
malawi	common	en	MALAWI
malaysia	common	en	MALAYSIA
maldives	common	en	MALDIVES
mali	common	en	MALI
malta	common	en	MALTA
manual	common	en	Manual
march	common	en	March
marshall islands	common	en	MARSHALL ISLANDS
martinique	common	en	MARTINIQUE
mauritania	common	en	MAURITANIA
mauritius	common	en	MAURITIUS
may	common	en	May
mayotte	common	en	MAYOTTE
medium	common	en	Medium
menu	common	en	Menu
message	common	en	Message
mexico	common	en	MEXICO
micronesia, federated states of	common	en	MICRONESIA, FEDERATED STATES OF
moldova, republic of	common	en	MOLDOVA, REPUBLIC OF
monaco	common	en	MONACO
monday	common	en	Monday
mongolia	common	en	MONGOLIA
montserrat	common	en	MONTSERRAT
morocco	common	en	MOROCCO
mozambique	common	en	MOZAMBIQUE
myanmar	common	en	MYANMAR
name	common	en	Name
name of the user, eg. %1	common	en	name of the user, eg. %1
namibia	common	en	NAMIBIA
nauru	common	en	NAURU
nepal	common	en	NEPAL
netherlands	common	en	NETHERLANDS
netherlands antilles	common	en	NETHERLANDS ANTILLES
never	common	en	Never
new caledonia	common	en	NEW CALEDONIA
new entry added sucessfully	common	en	New entry added sucessfully
new main category	common	en	New main category
new value	common	en	New Value
new zealand	common	en	NEW ZEALAND
next	common	en	Next
next month (hold for menu)	common	en	Next month (hold for menu)
next page	common	en	Next page
next year (hold for menu)	common	en	Next year (hold for menu)
nicaragua	common	en	NICARAGUA
niger	common	en	NIGER
nigeria	common	en	NIGERIA
niue	common	en	NIUE
no	common	en	No
no entries found, try again ...	common	en	no entries found, try again ...
no history for this record	common	en	No history for this record
no subject	common	en	No Subject
no themes found	common	en	No themes found
none	common	en	None
norfolk island	common	en	NORFOLK ISLAND
normal	common	en	Normal
northern mariana islands	common	en	NORTHERN MARIANA ISLANDS
norway	common	en	NORWAY
not assigned	common	en	not assigned
note	common	en	Note
notes	common	en	Notes
notify window	common	en	Notify Window
november	common	en	November
october	common	en	October
ok	common	en	OK
old value	common	en	Old Value
oman	common	en	OMAN
on *nix systems please type: %1	common	en	On *nix systems please type: %1
only private	common	en	only private
only yours	common	en	only yours
open notify window	common	en	Open notify window
open popup window	common	en	Open popup window
original	common	en	Original
other	common	en	Other
overview	common	en	Overview
owner	common	en	Owner
pakistan	common	en	PAKISTAN
palau	common	en	PALAU
palestinian territory, occupied	common	en	PALESTINIAN TERRITORY, OCCUPIED
panama	common	en	PANAMA
papua new guinea	common	en	PAPUA NEW GUINEA
paraguay	common	en	PARAGUAY
parcel	common	en	Parcel
parent category	common	en	Parent Category
password	common	en	Password
password could not be changed	common	en	Password could not be changed
password has been updated	common	en	Password has been updated
path to user and group files has to be outside of the webservers document-root!!!	common	en	Path to user and group files HAS TO BE OUTSIDE of the webservers document-root!!!
pattern for search in addressbook	common	en	Pattern for Search in Addressbook
pattern for search in calendar	common	en	Pattern for Search in Calendar
pattern for search in projects	common	en	Pattern for Search in Projects
permissions to the files/users directory	common	en	permissions to the files/users directory
personal	common	en	Personal
peru	common	en	PERU
philippines	common	en	PHILIPPINES
phone number	common	en	phone number
phpgroupware: login blocked for user '%1', ip %2	common	en	phpGroupWare: login blocked for user '%1', IP %2
phpgw-created account	common	en	phpgw-created account
phpgw-created group	common	en	phpgw-created group
pitcairn	common	en	PITCAIRN
please %1 by hand	common	en	Please %1 by hand
please enter a name	common	en	Please enter a name !
please run setup to become current	common	en	Please run setup to become current
please select	common	en	Please Select
please set your global preferences	common	en	Please set your global preferences !
please set your preferences for this application	common	en	Please set your preferences for this application !
please wait...	common	en	Please Wait...
poland	common	en	POLAND
portugal	common	en	PORTUGAL
postal	common	en	Postal
powered by phpgroupware version %1	common	en	Powered by <a href="http://www.phpgroupware.org">phpGroupWare</a> version %1
preferences	common	en	Preferences
prev. month (hold for menu)	common	en	Prev. month (hold for menu)
prev. year (hold for menu)	common	en	Prev. year (hold for menu)
previous page	common	en	Previous page
print	common	en	Print
priority	common	en	Priority
private	common	en	Private
project	common	en	Project
public	common	en	public
puerto rico	common	en	PUERTO RICO
qatar	common	en	QATAR
read	common	en	Read
read this list of methods.	common	en	Read this list of methods.
register_globals = %1	common	en	register_globals = %1
register_globals_off	common	en	This application has been tested to work with register_globals = off in your php.ini.  This application should work with most PHP 5.2.0+ servers.  If you experience any problems, please report them on our bug tracker at <a href="https://github.com/PorticoEstate/PorticoEstate/issues" target="_blank">savannah.gnu.org</a>.
register_globals_on	common	en	This application has not been tested to work with register_globals = off in your php.ini.  This application requires register_globals = on to be set in your php.ini.  For more information regarding the potential problem of using register_globals = on, see <a href="http://www.php.net/manual/en/security.registerglobals.php" target="_blank">php.net</a>.  If you experience any problems, please report them on our bug tracker at <a href="https://github.com/PorticoEstate/PorticoEstate/issues" target="_blank">savannah.gnu.org</a>.
reject	common	en	Reject
rename	common	en	Rename
returns a full list of accounts on the system.  warning: this is return can be quite large	common	en	Returns a full list of accounts on the system.  Warning: This is return can be quite large
returns an array of todo items	common	en	Returns an array of todo items
returns struct of users application access	common	en	Returns struct of users application access
reunion	common	en	REUNION
romania	common	en	ROMANIA
russian federation	common	en	RUSSIAN FEDERATION
rwanda	common	en	RWANDA
saint helena	common	en	SAINT HELENA
saint kitts and nevis	common	en	SAINT KITTS AND NEVIS
saint lucia	common	en	SAINT LUCIA
saint pierre and miquelon	common	en	SAINT PIERRE AND MIQUELON
saint vincent and the grenadines	common	en	SAINT VINCENT AND THE GRENADINES
samoa	common	en	SAMOA
san marino	common	en	SAN MARINO
sao tome and principe	common	en	SAO TOME AND PRINCIPE
saturday	common	en	Saturday
saudi arabia	common	en	SAUDI ARABIA
save	common	en	Save
search	common	en	Search
section	common	en	Section
select	common	en	Select
select category	common	en	Select Category
select date	common	en	Select date
select group	common	en	Select group
select one	common	en	Select one
select user	common	en	Select user
send	common	en	Send
senegal	common	en	SENEGAL
september	common	en	September
server %1 has been added	common	en	Server %1 has been added
server name	common	en	Server Name
session has been killed	common	en	Session has been killed
setup	common	en	Setup
seychelles	common	en	SEYCHELLES
show all	common	en	show all
showing %1	common	en	showing %1
showing %1 - %2 of %3	common	en	showing %1 - %2 of %3
sierra leone	common	en	SIERRA LEONE
singapore	common	en	SINGAPORE
slovakia	common	en	SLOVAKIA
slovenia	common	en	SLOVENIA
solomon islands	common	en	SOLOMON ISLANDS
somalia	common	en	SOMALIA
south africa	common	en	SOUTH AFRICA
south georgia and the south sandwich islands	common	en	SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS
spain	common	en	SPAIN
sri lanka	common	en	SRI LANKA
start date	common	en	Start date
start time	common	en	Start time
status	common	en	Status
subject	common	en	Subject
submit	common	en	Submit
substitutions and their meanings:	common	en	Substitutions and their meanings:
sudan	common	en	SUDAN
sunday	common	en	Sunday
suriname	common	en	SURINAME
svalbard and jan mayen	common	en	SVALBARD AND JAN MAYEN
swaziland	common	en	SWAZILAND
sweden	common	en	SWEDEN
switzerland	common	en	SWITZERLAND
syrian arab republic	common	en	SYRIAN ARAB REPUBLIC
taiwan, province of china	common	en	TAIWAN, PROVINCE OF CHINA
tajikistan	common	en	TAJIKISTAN
tanzania, united republic of	common	en	TANZANIA, UNITED REPUBLIC OF
thailand	common	en	THAILAND
the api is current	common	en	The API is current
the api requires an upgrade	common	en	The API requires an upgrade
the following applications require upgrades	common	en	The following applications require upgrades
the mail server returned	common	en	The mail server returned
this application is current	common	en	This application is current
this application requires an upgrade	common	en	This application requires an upgrade
this name has been used already	common	en	This name has been used already !
thursday	common	en	Thursday
time	common	en	Time
time zone	common	en	Timezone
time zone offset	common	en	Time zone offset
title	common	en	Title
to correct this error for the future you will need to properly set the	common	en	To correct this error for the future you will need to properly set the
to go back to the msg list, click <a href= %1 >here</a>	common	en	To go back to the msg list, click <a href=%1>here</a>
today	common	en	Today
todays date, eg. %1	common	en	todays date, eg. %1
toggle first day of week	common	en	Toggle first day of week
togo	common	en	TOGO
tokelau	common	en	TOKELAU
tonga	common	en	TONGA
too many unsuccessful attempts to login: %1 for the user '%2', %3 for the ip %4	common	en	Too many unsuccessful attempts to login: %1 for the user '%2', %3 for the IP %4
total	common	en	Total
trinidad and tobago	common	en	TRINIDAD AND TOBAGO
tuesday	common	en	Tuesday
tunisia	common	en	TUNISIA
turkey	common	en	TURKEY
turkmenistan	common	en	TURKMENISTAN
turks and caicos islands	common	en	TURKS AND CAICOS ISLANDS
tuvalu	common	en	TUVALU
uganda	common	en	UGANDA
ukraine	common	en	UKRAINE
united arab emirates	common	en	UNITED ARAB EMIRATES
united kingdom	common	en	UNITED KINGDOM
united states	common	en	UNITED STATES
united states minor outlying islands	common	en	UNITED STATES MINOR OUTLYING ISLANDS
unknown	common	en	Unknown
update	common	en	Update
url	common	en	URL
uruguay	common	en	URUGUAY
use button to search for	common	en	use Button to search for
use button to search for address	common	en	use Button to search for Address
use button to search for calendarevent	common	en	use Button to search for Calendarevent
use button to search for project	common	en	use Button to search for Project
user	common	en	User
user accounts	common	en	user accounts
user groups	common	en	user groups
username	common	en	Username
users	common	en	users
users choice	common	en	Users Choice
uzbekistan	common	en	UZBEKISTAN
vanuatu	common	en	VANUATU
venezuela	common	en	VENEZUELA
version	common	en	Version
viet nam	common	en	VIET NAM
view	common	en	View
virgin islands, british	common	en	VIRGIN ISLANDS, BRITISH
virgin islands, u.s.	common	en	VIRGIN ISLANDS, U.S.
wallis and futuna	common	en	WALLIS AND FUTUNA
wednesday	common	en	Wednesday
welcome	common	en	Welcome
western sahara	common	en	WESTERN SAHARA
which groups	common	en	Which groups
wk	common	en	wk
written by:	common	en	Written by:
year	common	en	Year
yemen	common	en	YEMEN
yes	common	en	Yes
you are required to change your password during your first login	common	en	You are required to change your password during your first login
you are running a newer version of phpgroupware than your database is setup for	common	en	You are running a newer version of phpGroupWare than your database is setup for.
you have not entered a title	common	en	You have not entered a title
you have not entered a valid date	common	en	You have not entered a valid date
you have not entered a valid time of day	common	en	You have not entered a valid time of day
you have not entered participants	common	en	You have not entered participants
you have selected an invalid date	common	en	You have selected an invalid date !
you have selected an invalid main category	common	en	You have selected an invalid main category !
you have successfully logged out	common	en	You have successfully logged out
your message could <b>not</b> be sent!<br />	common	en	Your message could <b>not</b> be sent!<br />
your message has been sent	common	en	Your message has been sent
your search returned %1 matchs	common	en	your search returned %1 matches
your search returned 1 match	common	en	your search returned 1 match
your settings have been updated	common	en	Your settings have been Updated
yugoslavia	common	en	YUGOSLAVIA
zambia	common	en	ZAMBIA
zimbabwe	common	en	ZIMBABWE
browser not supported	common	en	The browser seems to not support HTML5
no access	common	en	No Access
no data available in table	common	en	No data available in table
showing _start_ to _end_ of _total_ entries	common	en	Showing _START_ to _END_ of _TOTAL_ entries
showing 0 to 0 of 0 entries	common	en	Showing 0 to 0 of 0 entries
filtered from _max_ total entries	common	en	filtered from _MAX_ total entries
show _menu_ entries	common	en	Show _MENU_ entries
loading...	common	en	Loading...
processing...	common	en	Processing...
no matching records found	common	en	No matching records found
are you sure you want to delete this category ?	common	en	Are you sure you want to delete this category ?
installed applications	common	en	Installed applications
license	about	en	License
maintainer	about	en	Maintainer
sorry, your login has expired	login	en	Sorry, your login has expired
use a valid username and password to gain access to %1	login	en	Use a valid username and password to gain access to %1
new user	login	en	New User
forgotten password	login	en	Forgotten Password or Username
you have been successfully logged out	login	en	You have been successfully logged out
your session could not be verified.	login	en	Your session could not be verified.
edit user account	common	no	Rediger bruker konto
attachments	common	no	Vedlegg
add attachment	common	no	Legg til vedlegg
booking	common	no	Booking
bookingfrontend	common	no	Bookingfrontend
global configuration	common	no	Global konfigurasjon
global message	common	no	Globale beskjeder
manage users	common	no	Administrere brukere
manage groups	common	no	Administrere grupper
clear cache	common	no	Slett cache (minne)
global categories	common	no	Globale kategorier
view sessions	common	no	Vis sesjoner
view access log	common	no	Vis tilgangslogger
find and register all application hooks	common	no	Finn og register alle applikasjonskoblinger
asynchronous timed services	common	no	Asynkrone tidsbestemte tjenester
list users	common	no	brukerliste
list groups	common	no	gruppeliste
firstname	common	no	Fornavn
lastname	common	no	Etternavn
description	common	no	Beskrivelse
add sub	common	no	Legg til underkategori
color	common	no	Farge
apply	common	no	Aktiver
enabled	common	no	aktiv
no category	common	no	Ingen kategori
inactive	common	no	Inaktiv
inactive and hidden	common	no	Inaktiv og gjemt
status	common	no	Status
contact	common	no	Kontakt
user data	common	no	Bruker data
application	common	no	Applikasjon
user access	common	no	Brukertilgang
edit user	common	no	Rediger bruker
group data	common	no	Gruppe data
edit group	common	no	Rediger gruppe
group	common	no	Gruppe
all users	common	no	Alle brukere
members	common	no	Medlemer
applications	common	no	Applikasjoner
you do not have edit access to addressmaster contacts	common	no	Du har ikke tilgang til "AddresseMaster" kontakter
can change password	common	no	Kan bytte passord
anonymous user (not shown in list sessions)	common	no	Anonym bruker (vises ikke i sesjonslisten)
expires	common	no	Utløpsdato
never	common	no	Aldri
quota	common	no	Kvote
: activate to sort column ascending	common	no	: sorter kolonne stigende
: activate to sort column descending	common	no	: sorter kolonne synkende
features	common	no	Egenskaper
select none	common	no	Velg ingen
bookmarks	common	no	Snarveier
bookmark added	common	no	Snarvei er lagret
bookmark deleted	common	no	Snarvei er slettet
access	common	no	Access
access type	common	no	Access type
account has been created	common	no	Bruker har blitt opprettet
account has been deleted	common	no	Bruker har blitt slettet
account has been updated	common	no	Bruker har blitt oppdatert
add	common	no	Legg til
admin	common	no	Admin
administration	common	no	Administrasjon
april	common	no	April
are you sure you want to delete this entry ?	common	no	Er du sikker du vil slette denne posten ?
august	common	no	August
cancel	common	no	Avbryt
change	common	no	Endre
charset	common	no	utf-8
clear	common	no	Clear
clear form	common	no	Tøm skjema
configure access permissions	common	no	Tilgangskontroll
configuration	common	no	Konfigurasjon
copy	common	no	Kopier
create	common	no	Lag
created by	common	no	Laget av
current users	common	no	Current brukere
date	common	no	Dato
december	common	no	Desember
delete	common	no	Slett
domain	common	no	Database
domain name for mail-address, eg. %1	common	no	Domene-navnet for E-Postadressen, %1
done	common	no	Avbryt
e-mail	common	no	E-Post
email-address of the user, eg. %1	common	no	E-Postadressen til brukeren, %1
edit	common	no	Rediger
email	common	no	E-Post
entry has been deleted sucessfully	common	no	Entry har blitt slettet
entry updated sucessfully	common	no	Entry er oppdatert
error	common	no	Feil
exit	common	no	Avslutt
february	common	no	Februar
files	common	no	Filer
filter	common	no	Filter
filtered from _max_ total entries	common	no	filtrert fra totalt _MAX_ poster
first name	common	no	Fornavn
first name of the user, eg. %1	common	no	Fornavnet til brukeren, %1
first	common	no	Første
friday	common	no	Fredag
frontend	common	no	Frontend
ftp	common	no	FTP
global public	common	no	Global Public
group access	common	no	Gruppe Access
group has been added	common	no	Gruppe har blitt lagt til
group has been deleted	common	no	Gruppe har blitt slettet
group has been updated	common	no	Gruppe har blitt oppdatert
group public	common	no	Gruppe Public
groups	common	no	Grupper
help	common	no	Hjelp
high	common	no	Høy
home	common	no	Hjem
it has been more then %1 days since you changed your password	common	no	Det er mer enn %1 dager siden du har endet ditt passord
january	common	no	Januar
july	common	no	Juli
june	common	no	Juni
last name	common	no	Etternavn
last name of the user, eg. %1	common	no	Etternavn til bruker, %1
last page	common	no	siste side
last	common	no	Siste
loading...	common	no	Laster...
login	common	no	Login
logout	common	no	Logg ut
low	common	no	Lav
march	common	no	Mars
may	common	no	Mai
medium	common	no	Medium
monday	common	no	Mandag
name	common	no	Navn
name of the user, eg. %1	common	no	Fullt navn på bruker, %1
new entry added sucessfully	common	no	Ny entry er lagt til
next page	common	no	neste side
next	common	no	Neste
no	common	no	Nei
no matching records found	common	no	Ingen poster funnet som passer med søket
none	common	no	Ikke angitt
no data available in table	common	no	Ingen data i tabell
normal	common	no	Normal
november	common	no	November
october	common	no	Oktober
of total	common	no	Av totalt
of	common	no	av
ok	common	no	OK
only yours	common	no	kun dine
page prepared in %1 seconds.	common	no	Side produsert på %1 sekunder
password	common	no	Passord
password has been updated	common	no	Passord har blitt oppdatert
powered by phpgroupware version %1	common	no	Powered by <a href="http://www.phpgroupware.org">phpGroupWare</a> version %1
preferences	common	no	Innstillinger
previous page	common	no	Forrige side
prev	common	no	Forrige
print	common	no	Utskrift
priority	common	no	Prioritet
private	common	no	Privat
processing...	common	no	Prosesserer...
rename	common	no	Endre navn
rental	common	no	Leie
saturday	common	no	Lørdag
save	common	no	Lagre
search	common	no	Søk
september	common	no	September
session has been killed	common	no	Session har blitt avsluttet
show all	common	no	Vis alle
showing %1	common	no	showing %1
showing %1 - %2 of %3	common	no	showing %1 - %2 of %3
showing items	common	no	Viser
shows from	common	no	Viser fra
showing _start_ to _end_ of _total_ entries	common	no	Viser _START_ til _END_ av _TOTAL_ poster
showing 0 to 0 of 0 entries	common	no	Viser 0 til 0 av 0 poster
show _menu_ entries	common	no	Vis _MENU_ poster
site configuration	common	no	Konfigurasjon av moduler
sorry, there was a problem processing your request.	common	no	Beklager, der var problemer ved din forespørsel.
submit	common	no	Submit
sunday	common	no	Søndag
thursday	common	no	Torsdag
the api is current	common	no	API er oppdatert
the api requires an upgrade	common	no	API må oppdateres
this application is current	common	no	Denne modulen er oppdatert
this application requires an upgrade	common	no	Denne modulen må oppdateres
time	common	no	Tid
to	common	no	til
total	common	no	Total
tuesday	common	no	Tirsdag
updated	common	no	Oppdatert
view	common	no	Vis
wednesday	common	no	Onsdag
which groups	common	no	hvilke grupper
yes	common	no	Ja
you have 1 new message!	common	no	Du har 1 ny melding!
you have %1 new messages!	common	no	Du har %1 nye meldinger!
your message has been sent	common	no	Din melding har blitt sent
your search returned 1 match	common	no	ditt søk gav 1 treff
your search returned %1 matchs	common	no	ditt søk gav %1 treff
your settings have been updated	common	no	Dine innstillinger har blitt oppdatert
bad login or password	common	no	Ugyldig login eller passord
todays date, eg. %1	common	no	Dagens dato, %1
username	common	no	Brukernavn
version	common	no	versjon
password must be at least 8 characters long, not %1	common	no	Passord må være minst på 8 tegn, du forsøker med %1
password must contain at least 2 upper case characters	common	no	Passord må inneholde minst 2 store bosktaver
password must contain at least 2 lower case characters	common	no	Passord må inneholde minst 2 små bosktaver
password must contain at least 1 number	common	no	Passord må inneholde minst 1 tall
password must contain at least 1 non alphanumeric character	common	no	Passord må inneholde minst 1 spesialtegn
the passwords don't match	common	no	Passordene er ikke like
browser not supported	common	no	Nettleseren ser ikke ut til å støtte HTML5
outdated browser: %1	common	no	Utdatert nettleser: %1
custom fields	common	no	Egendefinerte felt
descr	common	no	beskrivelse
datatype	common	no	Datatype
sorting	common	no	Sortering
up	common	no	opp
down	common	no	ned
no access	common	no	Ingen Tilgang
toolbar	common	no	Verktøy
collection	common	no	Samling..
item	common	no	Valg
download visible data	common	no	last ned innholdet i synlig tabell til excel
download data	common	no	last ned alt filtrert innholdet til excel
previous week	common	no	Forrige uke
next week	common	no	Neste uke
week	common	no	Uke
week day	common	no	Ukedag
upload file	common	no	Last opp fil
upload multiple files	common	no	Last opp filer
upload files	common	no	Last opp filer
delete file	common	no	Slett fil
add files	common	no	Legg til filer
start upload	common	no	Start opplasting
cancel upload	common	no	Stopp opplasting
invalid file extension	common	no	Ugyldig filtype
number files	common	no	Antall filer
(filtered from _max_ total entries)	common	no	(filtrert fra _max_ poster)
bim	common	no	Bim
categories	common	no	Kategorier
char	common	no	Char
custom autocomplete::integer	common	no	Tilpasset autocomplete::ingeter
custom functions	common	no	Tilpassede funksjoner
custom listbox	common	no	Tilpasset listboks
custom menu items	common	no	Tilpassede menyer
datetime	common	no	Dato/tid
do you really want to delete this entry	common	no	Vil du slette denne posten
edit custom fields	common	no	Endre tilpassede felter
edit type	common	no	Endre type
float	common	no	Flyttall
home screen message	common	no	Melding på hjemmeside
hour	common	no	Time
integer	common	no	Heltall
link	common	no	Link
listbox	common	no	Nedtrekksliste
memo	common	no	Notat
muliple checkbox	common	no	Merkebokser
muliple radio	common	no	Flervalgs radio
my preferences	common	no	Mine innstillinger
organisation	common	no	Organsisasjon
select the category the data belong to. to do not use a category select no category	common	no	Velg kategorien posten tilhører. For ikke å bruke kategori, velg ingen kategori
sms	common	no	SMS
unable to load jquery script '%1' when attempting to load widget: '%2'	common	no	Ikke i stand til å laste  jquery script '%1' på forsøk på å laste widget: '%2'
varchar	common	no	Varchar
vendor	common	no	Leverandør
smtp server is not set! (admin section)	common	no	SMTP serveren er ikke konfigurert (admin seksjonen)
set permission	common	no	Definer rettigheter
right	common	no	Rettighet
mask	common	no	Maskering
result	common	no	Resultat
manage	common	no	Administrer
read	common	no	Les
select a location!	common	no	Velg en lokasjon
no location	common	no	Ingen lokasjon
enable inheritance	common	no	Aktiver arving
users	common	no	Brukere
maximum entries in click path history	common	no	Maksimum oppføringer i klikkveiledning
debugoutput	common	no	Debugoutput
add section	common	no	Legg til seksjon
showing	common	no	Viser
color selector	common	no	Fargevelger
select all	common	no	Velg alle
hidden	common	no	Gjemt
change your password	common	no	Endre passord
grant access	common	no	Gi tilgang
edit categories	common	no	Rediger kategorier
location	property	no	Lokalisering
login	login	no	Login
password	login	no	Passord
sorry, your login has expired	login	no	Beklager, din bruker er utgått
sorry, your session has expired	login	no	Beklager, sesjonen din er utløpt
use cookies	login	no	use cookies
new user	login	no	Ny bruker
forgotten password	login	no	Glemt passord eller brukernavn
use a valid username and password to gain access to %1	login	no	Angi et gyldig brukernavn og passord for få tilgang til %1
you have been successfully logged out	login	no	Du har nå logget ut
info: you have changed domain from "%1" to "%2"	login	no	Informasjon: Du har endret domene fra "%1" til "%2"
remark	home	no	Merknad
about %1	about	no	Om %1
tax code	property	no	MVA kode
loc1 name	property	no	Navn
link to the project originatet from this request	property	no	Link til prosjektet som har opprinnelse fra dette tiltaket
link to the project originatet from this ticket	property	no	Link til prosjektet som har opprinnelse fra denne hendelsen
link to the request for this project	property	no	Link til tiltak omfattet av dette prosjektet
link to the request originatet from this ticket	property	no	Link til tiltak som har opprinnelse fra dette hendelsen
link_view	property	no	Vis lenke
list	property	no	Oversikt
list %1	property	no	List %1
list activities per agreement	property	no	List aktiviteter pr avtale
list activities per agreement_group	property	no	List aktiviteter pr avtalegruppe
list agreement	property	no	list avtale
list agreement group	property	no	List avtalegruppe
list alarm	property	no	Oversikt alarm
list apartment	property	no	List leilighet
list async method	property	no	List async-metode
list attribute	property	no	List egenskaper
listbox	property	no	Listbox
list budget	property	no	Oversikt budsjett
list budget account	property	no	list kostnadsart
list building	property	no	list bygning
list claim	property	no	Oversikt klage
list config	property	no	List konfigurasjon
list consume	property	no	list forbruk
list custom	property	no	List tilpasset
list custom function	property	no	List egendefinerte funksjoner
list deviation	property	no	List avvik
list document	property	no	list dokumenter
list documents	property	no	Oversikt dokument
list entity attribute	property	no	List entitets-egenskaper
list entity custom function	property	no	List egendefinerte funskjoner for entiteter
list entity type	property	no	List entitet-type
list entrance	property	no	list inngang
list equipment	property	no	list utstyr
list gab	property	no	List grunneiendom
list gab detail	property	no	List grunneiendomsdetaljer
list hours	property	no	List timer
list investment	property	no	List investeringer
list invoice	property	no	List bilag
list location attribute	property	no	List lokaliserings egenskaper
list location standard	property	no	List lokaliserings standard
list meter	property	no	List måler
list obligations	property	no	Oversikt regnskap
list paid invoice	property	no	List betalte bilag
list pricebook	property	no	List prisbok
list pricebook per vendor	property	no	List prisbok pr leverandør
list project	property	no	List prosjekt
list property	property	no	List eiendom
list request	property	no	List behov
list standard description	property	no	List standard beskrivelse
list status	property	no	List status
list street	property	no	List gate
list template	property	no	List mal
list tenant	property	no	List leietaker
list ticket	property	no	List melding
list vendor	property	no	Oversikt leverandører
list vendors	property	no	List leverandør
location changed	property	no	Lokalisering er endret
location code	property	no	Lokaliserings kode
location_code	property	no	Lokaliserings kode
location config	property	no	Konfigurering av lokalisering
location form	property	no	Lokaliseringsskjema
location level	property	no	Lokaliserings-nivå
location link level	property	no	Fjern link til lokaliserings-nivå
location manager	property	no	Forvalter lokasjon
location name	property	no	Modul
location not chosen!	property	no	Lokalisering er ikke valgt
location type	property	no	Lokaliseringstype
location type not chosen!	property	no	Lokaliseringsnivå ikke valgt
log	property	no	Logg
longevity	property	no	Levetid
lookup	property	no	Slå opp
lookup template	property	no	Slå opp standardbeskrivelser
lookup tenant	property	no	Slå opp leietaker
lowest	property	no	Lavest
mailing method is not chosen! (admin section)	property	no	Mail metode er ikke valgt! (admin seksjon)
main claim	property	no	Hovedkrav
maintenance	property	no	Vedlikehold
make order	property	no	Lag bestilling
manage	property	no	Forvalte
manage groups	property	no	Administrere grupper
manage users	property	no	Administrere brukere
map	property	no	Kart
mark as draft	property	no	Merk som UTKAST
mark the tender as draft	property	no	Merk tilbud som UTKAST
mask	property	no	Masker
material cost	property	no	Materiell
materials:__________	property	no	Materialer:__________
m_cost	property	no	Materialkost
medium consequences	property	no	Middels store konsekvenser
member	property	no	Medlem
member of	property	no	Medlem av
memo	property	no	Memo
meter	property	no	Måler
meter %1 has been edited	property	no	Måler %1 er oppdatert
meter %1 has been saved	property	no	Måler %1 er lagret
meter id	property	no	Måler-id
meter type	property	no	Målertype
method	property	no	Metode
method has been edited	property	no	Metode er endret
method id	property	no	Metode-id
migrate to alternative db	property	no	Migrer til alternativ database
mine tickets	property	no	Mine meldinger
mine orders	property	no	Mine bestillinger
mine projects	property	no	Mine prosjekter
mine vouchers	property	no	Mine bilag
minor	property	no	Liten
minor consequences	property	no	Små konsekvenser
minute	property	no	Minutt
minutes before the event	property	no	Minutter til hendelse
missing log message	property	no	Angi grunn for endring
missing value	property	no	Mangler verdi
missing value for %1	property	no	Mangler verdi for %1
month	property	no	Måned
monthly (by day)	property	no	Månedlig (dag)
monthly (by date)	property	no	Månedlig (dato)
move	property	no	Flytt
move budget and orders to another project	property	no	Flytt budsjett og bestillinger til et annet prosjekt
move to another project	property	no	Flytt til et annet prosjekt
moved to another project	property	no	Flyttet til et annet prosjekt
muliple checkbox	property	no	Flervalgs avkrysningsknapp
muliple radio	property	no	Flervalgs radioknapp
multiple checkbox	property	no	Flervalgs avkrysningsknapp
multiple radio	property	no	Flervalgs radioknapp
multiplier	property	no	Skaleringsfaktor
my preferences	property	no	Mine innstillinger
my assigned tickets	property	no	Mine tildelte meldinger
my submitted tickets	property	no	Mine innmeldte meldinger
name not entered!	property	no	Navn er ikke angitt
name of the user, eg. %1	property	no	Navn til bruker, %1
narrow the search by dates	property	no	Avgrens søket med dato
needs approval	property	no	Behøver godkjenning
new	property	no	Ny
new grouping	property	no	Ny gruppering
new index	property	no	Ny index
new note	property	no	Ny kommentar
new organisation	common	no	Ny organisasjon
new person	common	no	Ny person
new_ticket	common	no	Ny melding
new value	property	no	Ny verdi
new value for multiple choice	property	no	Ny verdi for flervalg
new values	property	no	Nye verdier
new record	property	no	Ny post
next run	property	no	Neste kjøring
no access	property	no	Ingen tilgang
no account	property	no	Ingen konto
no additional notes	property	no	Ingen tilleggskommentarer
no branch	property	no	Fag ikke valgt
no category	property	no	Kategori ikke valgt
no change type	property	no	Ingen endringstype
no conversion type could be located.	property	no	Formatfilter ble ikke funnet
no custom function	property	no	Ingen egendfinert funksjon
no criteria	property	no	Kriterie ikke valgt
no datatype	property	no	Datatype ikke valgt
no dim b	property	no	Ansvarssted ikke valgt
no dim d	property	no	Dim 6 ikke valgt
no district	property	no	Distrikt ikke valgt
no document type	property	no	Ingen dokument-type
no entity type	property	no	Entitetstype ikke valgt
no equipment type	property	no	Utstyrstype ikke valgt
no file selected	property	no	Fil ikke valgt
no granting group	property	no	Ingen granting gruppe
no group	property	no	ingen gruppe
no grouping	property	no	Ingen gruppering
no history	property	no	Historie er ikke registert
no history for this record	property	no	Ingen historie for denne meldingen
no hour category	property	no	Ingen post-kategori
no janitor	property	no	Bestiller ikke valgt
no location	property	no	Lokalisering ikke valgt
no mailaddress is selected	property	no	E-post ikke valgt
no member	property	no	Ingen medlem
no method	property	no	Ingen metode
none consequences	property	no	Ingen konsekvenser
no part of town	property	no	Bydel ikke valgt
no revision	property	no	Ingen revisjon
no role	property	no	Rolle ikke valgt
no status	property	no	Status ikke valgt
no such order: %1	property	no	Ordre %1 finnes ikke
no supervisor	property	no	Attestant ikke valgt
not allocated	property	no	Ikke disponert
note	property	no	Notat
nothing to do!	property	no	Ingen ting å gjøre
notify	property	no	Varsle
no type	property	no	Type ikke valgt
no user	property	no	Bruker ikke valgt
no user selected	property	no	Bruker ikke valgt
no vendor	property	no	Leverandør ikke valgt
no workorder budget	property	no	Ingen budsjett bestilling
no workorder bugdet	property	no	Ingen bestillinger er budsjettert
no year	property	no	Ingen årstall
notify client by sms	property	no	Varsle kunde via SMS
ns3420	property	no	NS3420
ns3420 description	property	no	NS3420 beskrivelse
nullable	property	no	Kan være NULL
nullable not chosen!	property	no	Nullable ikke valgt
num	property	no	Num
number	property	no	Nummer
obligations	property	no	Regnskap
obligation	property	no	Forpliktet
of	property	no	av
on behalf of assigned	property	no	På vegne av tildelt
on behalf of assigned - vacation mode	property	no	På vegne av tildelt - feriemodus
old value	property	no	Opprinnelig verdi
only private	property	no	Bare privat
open	property	no	Åpen
open date	property	no	Startet dato
opened	property	no	Åpnet
opened by	property	no	Startet av
open edit in new window	property	no	Åpne endring i nytt vindu
open jasperreport %1 in new window	property	no	Åpne JasperReport %1 i nytt vindu
open view in new window	property	no	Åpne visning i nytt vindu
operation	property	no	Drift
o&m	property	no	D/V
order	property	no	Bestilling
order approval revoked	property	no	Bestillingsgodkjenning opphevet
order approved	property	no	Bestilling godkjent
order %1 approved for amount %2	property	no	Bestilling %1 er godkjent med beløp %2
order order %1 is not approved	property	no	Bestilling %1 er ikke godkjent
order_dim1	property	no	Aktivitet
order id	property	no	Bestillingsnr.
order_id	property	no	Bestillingsnr.
order # that initiated the invoice	property	no	Bestillingsnr som referanse for fakturaen
order template	property	no	Standardtekster
order text	property	no	Bestillingstekst
organisation	property	no	Organisasjon
other branch	property	no	Andre fag
override fraction	property	no	Overstyrt faktor
override fraction of common costs	property	no	Overstyringsfaktor for felleskostnader
overview	property	no	Oversikt
owner	property	no	Eier
owner attributes	property	no	Eier egenskaper
owner categories	property	no	Eier kategorier
owner type	property	no	Eiertype
paid	property	no	Betalt
paid percent	property	no	Prosentvis betalt
parked	property	no	Parkert
park invoice	property	no	Parker faktura
part of town	property	no	Bydel
part of town id	property	no	Bydel
payment	property	no	Betaling
payment date	property	no	Forfallsdato
pdf	property	no	PDF
per agreement	property	no	pr. avtale
percent	property	no	Prosent
percentage addition	property	no	prosentvis tillegg
performed	property	no	Utført
period	property	no	Periode
periods	property	no	Perioder
period transition	property	no	Periode overgang
permission	property	no	Rettighet
permissions	property	no	Rettigheter
permissions are updated!	property	no	Rettigheter er oppdatert
per vendor	property	no	pr. leverandør
phone	property	no	Telefon
php configuration	common	no	php konfigurasjon
phpinfo	common	no	phpinformasjon
picture	property	no	Bilde
plain	property	no	Vanlig
planned cost	property	no	Planlagt
pleace select a location - or an equipment !	property	no	Velg en lokalisering - eller et utstyr
please select a condition!	property	no	Angi tilstand
please select a building part!	property	no	Angi bygningsdel
please choose a conversion type	property	no	Velg eksport type
please choose a conversion type from the list	property	no	Velg eksport type fra listen
please choose a file	property	no	Velg en fil
please either select generate id or type a equipment id !	property	no	enten velg GENERER-id eller angi et utstyrs-id
please enter a apartment id !	property	no	Angi leilighets-id
please enter a building id !	property	no	Angi bygg-id
please enter a invoice num!	property	no	Angi fakturanummer
please enter a description!	property	no	Angi en beskrivelse!
please enter a entrance id !	property	no	Angi inngangs-id
please enter a index !	property	no	Angi en indeks
please enter a title !	property	no	Angi en overskrift
please enter an activity code !	property	no	Angi en aktivitets kode
please enter an agreement code !	property	no	Angi en avtale kode
please enter an agreement group code !	property	no	Angi en avtale-gruppe-kode
please enter a name !	property	no	Angi et navn
please - enter an amount!	property	no	Angi et beløp!
please enter a new index for calkulating next value(s)!	property	no	Angi en ny index for beregning av neste verdi(er)!
please enter an integer !	property	no	Angi heltall
please enter an integer for order!	property	no	Angi heltall for bestillingsnr
please enter a project name !	property	no	Angi prosjekt navn
please enter a property id !	property	no	Angi eiendoms-id
please enter a request title !	property	no	Angi tittel for behov
please enter a sql query !	property	no	Angi en sql-spørring
please enter a value for either material cost, labour cost or both !	property	no	Angi enten material kostnader, arbeidskostnader eller begge
please enter a workorder title !	property	no	Angi en bestillings tittel
please enter either a budget or contrakt sum	property	no	Angi enten budsjett eller konstraktsum (eller begge)
please enter value for attribute %1	property	no	Angi verdi for felt %1
please enter integer for attribute %1	property	no	Angi heltall for felt %1
please enter precision as integer !	property	no	Angi heltall for presisjon
please enter scale as integer !	property	no	Angi heltall for skalering
please give som details !	property	no	Angi detaljer
please select a branch !	property	no	Velg fag
please select a budget account !	property	no	Velg en kostnadsart
please select a valid budget account !	property	no	Velg en gyldig kostnadsart
please select a budget reponsible!	property	no	Velg en anviser
please select a building id !	property	no	Velg bygnings-id
please select a category !	property	no	Velg kategori
please select a coordinator !	property	no	Velg en koordinator
please select a date !	property	no	Velg dato
please select a district !	property	no	Velg et distrikt
please select a entrance id !	property	no	Velg inngangs-id
please - select a file to import from !	property	no	Velg en fil å importere fra
please select a file to upload !	property	no	Velg en fil for opplasting
please - select a import format !	property	no	Velg et import format
please select a location !	property	no	Velg lokalisering
please select a location - or an entity!	property	no	Velg en lokalisering eller en entitet
please select an agreement !	property	no	Velg en avtale
please select an agreement_group !	property	no	Velg en avtale-gruppe
please select an end date!	property	no	Angi slutt-dato
please select a period for write off !	property	no	Velg en avskrivningsperiode
please select a person or a group to handle the ticket !	property	no	Velg en person eller gruppe for tildeling av melding
please select a person to handle the ticket !	property	no	Velg en person som saksbehandler for denne meldingen
please select a property id !	property	no	Velg en eiendoms-id
please select a status !	property	no	Velg status
please select a type !	property	no	Velg type
please select a valid project !	property	no	Velg et gyldig prosjekt
please - select a vendor!	property	no	Velg en leverandør
please select a workorder !	property	no	Velg en ordre!
please - select budget responsible!	property	no	Velg en anviser
please select change type	property	no	Velg en endringstype
please - select either payment date or number of days from invoice date !	property	no	Velg enten betalingsdato eller angi antall dager till forfall fra fakturadato
please select entity type !	property	no	Velg en entitets-type
please select equipment type !	property	no	Velg utstyrstype
please select type	property	no	Velg type
please - select type invoice!	property	no	Velg type bilag
please - select type order!	property	no	Velg type bestilling
please - select vendor!	property	no	Velg leverandør
please set a initial value !	property	no	Sett initiell verdi
please set a new index !	property	no	Angi ny index
please set default assign to in preferences for user %1!	property	no	Angi standardverdi for ansvarlig saksbehandler for bruker %1
please set default category in preferences for user %1!	property	no	Angi standardverdi for kategori for bruker %1
please type a subject for this ticket !	property	no	Angi overskrift for denne meldingen
popup calendar	property	no	Kalender
post	property	no	post
power meter	property	no	Strømmåler
potential grants	property	no	Potensiale offentlig tilskudd
precision	property	no	Presisjon
prefix	property	no	prefiks
preview html	property	no	Forhåndsvis som HTML
preview pdf	property	no	Forhåndsvis som PDF
presumed finnish date	property	no	Antatt ferdig-dato
pricebook	property	no	prisbok
print view	property	no	Vis utskrift
priority changed	property	no	Prioritet er endret
priority key	property	no	Prioriteringsnøkkel
priority keys has been updated	property	no	Prioriteringsnøkkel er oppdatert
prizing	property	no	Priser
probability	property	no	Sannsynlighet
project	property	no	Prosjekt
project type	property	no	Prosjekt type
.project	property	no	Prosjekt
project.condition_survey	property	no	Tilstandsanalyse
project.workorder	property	no	Prosjekt::Bestilling
project %1 has been edited	property	no	Prosjekt %1 er oppdatert
project %1 has been saved	property	no	Prosjekt %1 er lagret
project %1 needs approval	property	no	Prosjekt %1 venter på godkjenning
project budget	property	no	Prosjekt budsjett
project categories	property	no	Prosjekt-kategorier
project coordinator	property	no	Prosjekt-ansvarlig
project end date	property	no	Prosjekt-sluttdato
project group	property	no	Agresso prosjekt
project id	property	no	Prosjekt-id
project info	property	no	Prosjekt info.
project is closed	property	no	Prosjektet er avsluttet.
project name	property	no	Prosjekt navn
.project.request	property	no	Behov
project start date	property	no	Prosjekt-startdato
.project.workorder	property	no	Bestilling
propagate	property	no	Propager
property	common	no	Eiendom
property has been edited	property	no	Eiendom er oppdatert
property has been saved	property	no	Eiendom er lagret
property id	property	no	eiendoms-id
property name	property	no	Eiendomsnavn
public	property	no	Vises for alle
publish text	property	no	Publiser tekst
purchase cost	property	no	Anskaffelseskostnad
quantity	property	no	Mengde
read	property	no	Les
ready for processing claim	property	no	Til fakturering
re-assigned	property	no	Videresendt
re-assigned group	property	no	Tildelt ny gruppe
receipt	property	no	Kvittering
receipt date	property	no	Kvitteringsdato
reconciliation	property	no	Avstemming
recommended year	property	no	Anbefalt tiltaksår
record	property	no	Post
reference level	property	no	Referanse nivå
regulations	property	no	Forskrifter
related	property	no	Relatert
related info	property	no	Relatert info
remark	property	no	Merknad
remainder	property	no	Rest
reminder	property	no	Påminning
rental	property	no	Utleie
rental agreement	property	no	Utleieavtale
rental agreement attributes	property	no	Leieavtale-egenskaper
rental agreement categories	property	no	Leieavtale-kategorier
rental agreement item attributes	property	no	Egenskaper for utleie - detaljer
rental type	property	no	Utleietype
re-opened	property	no	Gjenåpnet
repeat	property	no	Serie
repeat type	property	no	Type serie
repeat day	property	no	Gjenta dag
request	property	no	Behov
set the status of the request	property	no	Sett status for behovet
reset approval	property	no	Nullstill
project.request	property	no	Behov
request attributes	property	no	Behov egenskaper
request an email receipt	property	no	Be om kvittering
request a confirmation email when your email is opened by the recipient	property	no	Be om epost som kvittering når mottaker åpner eposten.
request %1 has been edited	property	no	Behov %1 er oppdatert
request %1 has been saved	property	no	Behov %1 er lagret
request %1 has been deleted from project %2	property	no	Behov %1 er koplet fra prosjekt %2
request %1 has been added	property	no	Behov %1 er lagt til
request %1 has already been added to project %2	property	no	Behov %1 er allerede koplet til prosjekt %2
request budget	property	no	Budsjett for tiltak
request condition type	property	no	Behov::konsekvenstype
request descr	property	no	Beskrivelse av tiltak
request end date	property	no	Behov frist
request entry date	property	no	Registreringsdato for tiltak
request id	property	no	Behov-id
request start date	property	no	Behov startdato
request status	property	no	Status for behov
request title	property	no	Tiltakstittel
request description	property	no	Tilstandbeskrivelse
requirement	property	no	Behov/pålegg
resend workorder	property	no	Send ordre på nytt
reserve	property	no	Reserve
reserve remainder	property	no	Rest av reserve
reset	property	no	Tilbakestill
residential environment	property	no	Bo-miljø
residual demand	property	no	Rest behov
reponse template	property	no	SMS::mal for standardtekster
responsible	property	no	Ansvarlig
responsibility	property	no	Ansvar
responsible matrix	property	no	Ansvarsmatrise
responsibility role	property	no	Rolle for ansvarsmatrise
responsible unit	property	no	Ansvarlig enhet
result	property	no	Resultat
return back to the list	property	no	Returner til oversikt
revision	property	no	Revisjon
rig addition	property	no	Rigg tillegg
right	property	no	Rettighet
role	property	no	Rolle
roles	property	no	Roller
roll back	property	no	Rull tilbake
rollback invoice	property	no	Rull  tilbake bilag
run now	property	no	Kjør nå
run the method now	property	no	Kjør metoden nå
safety	property	no	Sikkerhet
save as template	property	no	lagre som mal
save the actor and return back to the list	property	no	Lagre aktøren og returner til oversikten
save the agreement and return back to the list	property	no	Lagre avtalen og returner til oversikten
save the apartment	property	no	Lagre leilighet
save the attribute	property	no	Lagre egenskap
save the budget account	property	no	Lagre budsjettkotntoen
save the building	property	no	lagre bygning
save the category	property	no	Lagre kategorien
save the claim and return back to the list	property	no	Lagre kravet og returner til oversikten
save the custom and return back to the list	property	no	Lagre egendefinert og returner til oversikten
template	property	no	Mal
save the custom function	property	no	Lagre egendefinert funksjon
save the deviation	property	no	Lagre avvik
save the document	property	no	lagre dokument
save the entity	property	no	Lagre entitet
save the entrance	property	no	Lagre inngang
save the entry and return to list	property	no	Lagre data og returner til hovedliste
save the equipment	property	no	Lagre utstyr
save the gab	property	no	Lagre grunneiendom
save the investment	property	no	lagre investering
save the location	property	no	Lagre lokalisering
save the meter	property	no	lagre måler
save the method	property	no	Lagre metoden
save the owner and return back to the list	property	no	Lagre eier og returner til oversikten
save the part of town and return back to the list	property	no	Lagre bydel og returner til oversikten
save the project	property	no	Lagre prosjekt
save the property	property	no	Lagre eiendom
save the rental agreement and return back to the list	property	no	Lagre utleieavtale og returner til oversikten
save the request	property	no	Lagre behov
save the service agreement and return back to the list	property	no	Lagre serviceavtale og returner til oversikten
save the standard	property	no	Lagre standard
save the status	property	no	Lagre status
save the ticket	property	no	Lagre melding og bli stående i skjema
save the voucher	property	no	Lagre bilag
save the workorder	property	no	Lagre bestilling
save this workorder as a template for later use	property	no	Lagre denne bestillingen som mal for senere bruk
save values and exit	property	no	Lagre verdier og gå til liste
save new	property	no	Lagre og ny
scale	property	no	Scale
schedule	property	no	Planlegge (tid)
scheduled events	property	no	Planlagte oppgaver
schedule the method	property	no	Tidsplanlegging
score	property	no	Poengsum
search by bruk. to show all entries, empty all fields and press the submit button again	property	no	Søk etter bruksnr.
search by feste. to show all entries, empty all fields and press the submit button again	property	no	Søk etter festenr.
search by gaards nr. to show all entries, empty all fields and press the submit button again	property	no	Søk etter gårdsnr.
search by location_code. to show all entries, empty all fields and press the submit button again	property	no	Søk etter lokaliseringskode
search by property	property	no	Søk ved eiendom
search by seksjon. to show all entries, empty all fields and press the submit button again	property	no	Søk etter seksjon
search criteria	property	no	Søkekriterie
search for history at this location	property	no	Søk etter historikk for denne lokaliseringen
search for investment entries	property	no	Søk etter investeringsposter
search for paid invoices	property	no	Søk etter betalte bilag
search for voucher id	property	no	Søk etter bilagsnr.
see attachment	property	no	Se vedlegg
seksjons nr	property	no	Seksjonsnr.
select	property	no	Velg
select a actor type	property	no	Velg en aktør-type
select a agreement type	property	no	Velg en avtale-type
select a custom function	property	no	Velg en egendefinert funsksjon
select a datatype	property	no	Velg en datatype
select a entity type	property	no	Velg en entitetstype
select agreement	property	no	Velg avtale
select agreement group	property	no	Velg en avtale-gruppe
select agreement_group	property	no	Velg avtalegruppe
select all	property	no	Velg alle
select none	property	no	Velg ingen
select a location	property	no	Velg en lokalisering
select a location!	property	no	Velg en lokalisering
select a value	property	no	Velg en verdi
select a rental agreement type	property	no	Velg en leieavtale-type
select a service agreement type	property	no	Velg en serviceavtale-type
select a standard-code from the norwegian standard	property	no	Velg en standard NS-kode
select a tenant	property	no	Velg en leietaker
select branch	property	no	Velg fag
select b-responsible	property	no	Velg anviser
select building part	property	no	Velg bygningsdel
select category	property	no	Velg kategori
select chapter	property	no	Velg kapittel
select column	property	no	Velg kolonne
select conversion	property	no	Velg filter
select coordinator	property	no	Velg koordinator
select date	property	no	Velg dato
select date for the file to roll back	property	no	Velg dato for fil som skal rulles tibake
select date the document was created	property	no	Velg dato for opprettelse av dokumentet
select default vendor category	property	no	Velg standard leverandør-kategori
select either a location or an entity	property	no	Velg enten en lokalisering eller en entitet
select either a location or an equipment	property	no	Velg enten en lokalisering eller ett utstyr
select email	property	no	Velg e-post
select file to roll back	property	no	Velg fil for tilbakerulling
select file to upload	property	no	Velg fil for opplasting
select files	property	no	Velg filer
select grouping	property	no	Velg gruppering
select invoice type	property	no	Velg art
select key responsible	property	no	Velg nøkkelansvarlig
select location level	property	no	Velg lokaliseringsnivå
select nullable	property	no	Velg nullable
select owner	property	no	Velg eier
select per button !	property	no	Velg ved hjelp av knappen !
select rental type	property	no	Velg utleietype
select request	property	no	Velg behov
time created	property	no	laget tid
select responsible	property	no	Velg ansvarlig
select status	property	no	Velg status
select submodule	property	no	Velg submodul
select the account class the selection belongs to	property	no	Velg kontoklasse
select the agreement_group the pricebook belongs to. to do not use a category select no category	property	no	Velg avtalegruppen denne prisboken tilhører
select the agreement group this activity belongs to.	property	no	Velg avtalegruppen denne aktiviteten tilhører
select the agreement the pricebook belongs to. to do not use a category select no category	property	no	Velg avtale denne prisboken tilhører
select the agreement this activity belongs to.	property	no	Velg avtale denne aktiviteten tilhører
select the appropriate condition degree	property	no	Velg tilstandsgrad
select the appropriate consequence by breakdown of this component for this theme	property	no	Velg konsekvens ved sammenbrudd av dennekomponenten for dette temaet
select the appropriate propability for worsening of the condition	property	no	Velg sannsynlighet for forverring
select the appropriate tax code	property	no	Velg mva-kode
select the branches for this project	property	no	Velg fag for dette prosjektet
select the branches for this request	property	no	Velg fag for dette behovet
select the branch for this activity.	property	no	Velg fag for denne aktiviteten
select the branch for this document	property	no	Velg fag for dette dokumentet
select the budget responsible	property	no	Velg anviser
select the building part for this activity.	property	no	Velg bygningsdel for denne aktiviteten
select the category the actor belongs to. to do not use a category select no category	property	no	Velg kategorien denne aktøren tilhører
select the category the agreement belongs to. to do not use a category select no category	property	no	Velg kategorien denne avtalen tilhører
select the category the alarm belongs to. to do not use a category select no category	property	no	Velg kategorien denne alarmen tilhører
select the category the apartment belongs to. to do not use a category select no category	property	no	Velg kategori leilighetene tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the building belongs to. to do not use a category select no category	property	no	Velg kategori bygningene tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the claim belongs to. to do not use a category select no category	property	no	Velg kategorien dette kravet tilhører
select the category the custom belongs to. to do not use a category select no category	property	no	Velg kategorien denne egentilpassede tilhører
select the category the data belong to. to do not use a category select no category	property	no	Velg kategorien posten tilhører
select the category the document belongs to. to do not use a category select no category	property	no	Velg kategori dokumentene tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the entrance belongs to. to do not use a category select no category	property	no	Velg kategori inngang tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the equipment belongs to. to do not use a category select no category	property	no	Velg kategori utstyr tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the investment belongs to. to do not use a category select no category	property	no	Velg kategori investering tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the location belongs to. to do not use a category select no category	property	no	Velg kategorien denne lokaliseringen tilhører
select the category the meter belongs to. to do not use a category select no category	property	no	Velg kategori måler tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the permissions belongs to. to do not use a category select no category	property	no	Velg kategorien denne rettigheten tilhører
select the category the pricebook belongs to. to do not use a category select no category	property	no	Velg kategori prisbok tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the project belongs to. to do not use a category select no category	property	no	Velg kategori prosjekt tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the property belongs to. to do not use a category select no category	property	no	Velg kategori eiendom tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the r_agreement belongs to. to do not use a category select no category	property	no	Velg kategorien denne utleieavtalen tilhører
select the category the request belongs to. to do not use a category select no category	property	no	Velg kategori behovet tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the s_agreement belongs to. to do not use a category select no category	property	no	Velg kategorien denne serviceavtalen tilhører
select the category the ticket belongs to. to do not use a category select no category	property	no	Velg kategori melding tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category the workorder belongs to. to do not use a category select no category	property	no	Velg kategori bestillingen tilhører. For ikke å bruke kategori velg KATEGORI IKKE VALGT
select the category. to do not use a category select no category	property	no	Velg kategori
select the chapter (for tender) for this activity.	property	no	Velg tilbudskapittel for denne posten
select the coordinator the document belongs to. to do not use a category select no user	property	no	Velg koordinator dokument tilhører. For ikke å bruke koordinator velg BRUKER IKKE VALG
select the coordinator the project belongs to. to do not use a category select no user	property	no	Velg koordinator prosjekt tilhører. For ikke å bruke koordinator velg BRUKER IKKE VALG
select the coordinator the request belongs to. to do not use a category select no user	property	no	Velg koordinator behovet tilhører. For ikke å bruke koordinator velg BRUKER IKKE VALG
select the customer by clicking this link	property	no	Velg kunde
select the date for the first value	property	no	Angi dato for første verdi
select the date for the update	property	no	Angi dato for oppdatering
select the vendor the s_agreement belongs to.	property	no	Velg leverandør
select the workorder hour category	property	no	Velg post-kategori
select the dim b for this invoice. to do not use dim b -  select no dim b	property	no	Velg Ansvarssted for bilag. For ikke å bruke Ansvarssted - velg Ansvarssted IKKE VALGT
select the dim d for this activity. to do not use dim d -  select no dim d	property	no	Velg DIM 6 for bilag. For ikke å bruke DIM 6 - velg DIMD IKKE VALGT
select the district the part of town belongs to.	property	no	Velg hvilke distrikt denne bydelen tilhører
select the district the selection belongs to. to do not use a district select no district	property	no	Velg distrikt utvalget tilhører. For ikke å bruke distrikt velg DISTRIKT IKKE VALGT
select the document type the document belongs to.	property	no	Velg dokumenttype for dette dokumentet
select the equipment type the document belongs to. to do not use a type select no equipment type	property	no	Velg utstyrstype dokumenter tilhøre.rFor ikke å bruke utstyrstype velg TYPE IKKE VALGT
select the estimated date for closing the task	property	no	Velg beregnet dato for lukking av oppgave
select the estimated end date for the agreement	property	no	Angi estimert sluttdato for avtalen
select the estimated end date for the project	property	no	Angi estimert sluttdato for prosjektet
select the estimated end date for the request	property	no	Angi estimert sluttdato for behovet
select the estimated start date for the request	property	no	Angi ønsket startdato for behovet
select the estimated termination date	property	no	Angi estimert terminerings-dato
select the file to import from	property	no	Velg fil å importere
select the filter. to show all entries select show all	property	no	Velg filter. For å vise alle - velg VIS ALLE
select the granting group. to do not use a granting group select no granting group	property	no	Angi tildelingsgruppe
select the grouping for this activity.	property	no	Velg gruppering for denne posten
select the grouping the selection belongs to	property	no	Velg gruppering utvelgelsen gjelder
select the janitor responsible for this invoice. to do not use janitor -  select no janitor	property	no	Velg bestiller bilaget tilhører. For ikke å tildele bestiller velg BESTILLER IKKE VALG
select the key responsible for this project	property	no	Velg nøkkelansvarlig
select the level for this information	property	no	Velg nivå for denne informasjonen
select the method for this times service	property	no	Velg metode
select the owner	property	no	Velg eier
select the owner type. to show all entries select show all	property	no	Velg eiertype
select the part of town the building belongs to. to do not use a part of town -  select no part of town	property	no	Velg bydel bygg tilhører. For ikke å bruke bydel velg BYDEL IKKE VALGT
select the part of town the investment belongs to. to do not use a part of town -  select no part of town	property	no	Velg bydel investeriong tilhører. For ikke å bruke bydel velg BYDEL IKKE VALGT
select the part of town the property belongs to. to do not use a part of town -  select no part of town	property	no	Velg bydel eiendom tilhører. For ikke å bruke bydel velg BYDEL IKKE VALGT
select the part of town the selection belongs to. to do not use a part of town select no part of town	property	no	Velg bydel
select the priority the selection belongs to.	property	no	Velg prioritet
select the property by clicking this link	property	no	klikk her for å velge eiendom
select the revision the selection belongs to	property	no	Velg revisjonen utvelgelsen gjelder
select the status the agreement belongs to. to do not use a category select no status	property	no	Velg status avtale tilhører. For ikke å bruke status velg STATUS IKKE VALGT
select the status the agreement group belongs to. to do not use a category select no status	property	no	Velg status
select the status the document belongs to. to do not use a category select no status	property	no	Velg status dokument tilhører. For ikke å bruke status velg STATUS IKKE VALGT
select the status. to do not use a status select no status	property	no	Velg status
select the street name	property	no	Velg gatenavn
select the supervisor responsible for this invoice. to do not use supervisor -  select no supervisor	property	no	Velg attestant bilaget tilhører. For ikke å tildele bestiller velg ATTESTANT IKKE VALG
select the template-chapter	property	no	Velg kapittel
select the tolerance for this activity.	property	no	Velg toleranse for denne posten
select the type  invoice. to do not use type -  select no type	property	no	Velg type
select the type of conversion:	property	no	Velg type import:
select the type of value	property	no	Velg type verdi
select the unit for this activity.	property	no	Velg enhet for post
select the users supervisor	property	no	Velg brukers overordnet
select the user the alarm belongs to.	property	no	Velg bruker
select the user the document belongs to. to do not use a category select no user	property	no	Velg bruker dokument tilhører. For ikke å bruke bruker velg BRUKER IKKE VALG
select the user the project belongs to. to do not use a category select no user	property	no	Velg bruker prosjekt tilhører. For ikke å bruke bruker velg BRUKER IKKE VALG
select the user the request belongs to. to do not use a category select no user	property	no	Velg bruker behovet tilhører. For ikke å bruke bruker velg BRUKER IKKE VALG
select the user the selection belongs to. to do not use a user select no user	property	no	Velg bruker utvalg tilhører. For ikke å bruke bruker velg BRUKER IKKE VALG
select the user the template belongs to. to do not use a category select no user	property	no	Velg bruker mal tilhører. For ikke å bruke bruker velg BRUKER IKKE VALG
select the user the workorder belongs to. to do not use a category select no user	property	no	Velg bruker bestillingen tilhører. For ikke å bruke bruker velg BRUKER IKKE VALG
select the user. to do not use a category select no user	property	no	Velg bruker
select the user to edit email	property	no	Velg bruker for å rette epost
select the vendor by clicking the button	property	no	Velg leverandør
select the vendor by clicking this button	property	no	Velg leverandør
select the vendor by clicking this link	property	no	Velg leverandør
select the vendor the agreement belongs to.	property	no	Velg leverandør for avtalen
select the vendor the r_agreement belongs to.	property	no	Velg leverandør
select the year the selection belongs to	property	no	Velg år utvelgelsen gjelder
select this budget account	property	no	Velg kostnadsart
select this contact	property	no	Velg denne kontakten
select this dates	property	no	Velg dato
select this ns3420 - code	property	no	Velg NS-kode
select this street	property	no	Velg gate
select this template to view the details	property	no	Velg mal for å liste detaljer
select this tenant	property	no	Velg leietaker
select this vendor	property	no	Velg denne leverandøren
select tolerance	property	no	Velg toleranse
select unit	property	no	Velg enhet
select user	property	no	Velg bruker
select where to deliver the key	property	no	Velg hvor nøkkel skal leveres
select where to fetch the key	property	no	Velg hvor nøkkel kan hentes
select year	property	no	Velg år
send	property	no	Send
send as pdf	property	no	Send som PDF
send e-mail	property	no	Send epost
send order	property	no	Send ordre
send pdf as attachment to email	property	no	Send PDF som vedlegg til epost
sent by email to	property	no	Sendt med e-post til
sent by sms	property	no	Sendt med SMS
send the following sms-message to %1 to update status for this order:	property	no	Send følgende SMS-melding til %1 for å oppdatere status for denne ordren:
send this order by email	property	no	Send denne ordren med e-post
send workorder	property	no	Send ordre
serious	property	no	Kraftig
serious consequences	property	no	Store konsekvenser
service	property	no	Tjeneste
service agreement	property	no	Serviceavtale
service agreement attributes	property	no	Serviceavtale egenskaper
service agreement categories	property	no	Serviceavtale kategorier
service agreement item attributes	property	no	Egenskaper for serviceavtaler - detaljer
set grants	property	no	Sett tillatelser
set new status	property	no	Sett ny status
set tax	property	no	Angi MVA
set tax during import	property	no	Registrer mva ifm import
set the status of the ticket	property	no	Sett status for meldingen
shared use	property	no	Delt bruk
shift down	property	no	Skift ned
shift up	property	no	Skift opp
show all entities	common	no	Vis alle poster
show calculated cost	property	no	Vis sum
show calculated cost on the printview	property	no	Vis sum på utskrift
show details	property	no	Vis detaljer
show in list	property	no	Vis i oversikt
show in lookup forms	property	no	Vis i oppslagsskjema
site configuration	property	no	Konfigurasjon nettsted
small	property	no	Liten
sms	sms	no	SMS
sorting	property	no	Sortering
sort the tickets by their id	property	no	sorter meldinger etter-id
sort the tickets by their priority	property	no	sorter meldinger etter prioritet
space	property	no	Areal
split line	property	no	Splitt linje
sql	property	no	SQL
standard	property	no	standard
standard description	property	no	NS 3420
standard has been edited	property	no	Standard er rettet
standard has been saved	property	no	Standard er lagret
standard has not been edited	property	no	Standard er ikke rettet
standard id	property	no	Standard-id
standard prefix	property	no	Prefiks fo standard
start	property	no	Start
start date	property	no	Startdato
started	property	no	Startet
start project	property	no	Start prosjekt
start this entity	property	no	Start denne entiteten
start this entity from	property	no	Start denne entiteten fra prosjekt
start ticket	property	no	Start melding
started from	property	no	Startet fra
status	property	no	Status
status changed	property	no	Status er endret
status code	property	no	Statuskode
status confirmed	property	no	Status bekreftet
status for the entity category	property	no	Status for entitets-kategorien
status has been added	property	no	Status er lagt til
status has been edited	property	no	Status er rettet
status has not been saved	property	no	Status er IKKE lagret!
status id	property	no	Status-id
statustext	property	no	Statustekst
statustext not entered!	property	no	Statustekst er ikke angitt!
street name	property	no	Gatenavn
street number	property	no	Gatenr.
subject	property	no	Overskrift
subject changed	property	no	Emne er endret
subject has been updated	property	no	Overskrift er oppdatert
submit the search string	property	no	Send søkestrengen
sum estimated cost	property	no	Sum estimert kostnad
sum orders	property	no	Forpliktet
sum	property	no	Sum
sum calculation	property	no	Sum kalkulasjon
sum deviation	property	no	Sum avvik
summary	property	no	Sammendrag
summation	property	no	Summering
sum of calculation	property	no	Sum kalkulasjon
sum tax	property	no	Sum mva
sum workorder	property	no	Sum bestilling
supervisor	property	no	Attestant
sync account-contact	common	no	Synkroniser brukere og kontakter
table could not be added	property	no	Tabell kunne ikke legges til i databasen
table has not been saved	property	no	Tabell er ikke lagret
table name	property	no	Tabellnavn
take over	property	no	Ta over
take over the assignment for this ticket	property	no	Ta over ansvaret for denne saken
template %1 is added	property	no	Mal %1 er lagt til
template id	property	no	Mal-id
tenant	property	no	Leietaker
tenant attributes	property	no	Leietaker egenskaper
tenant categories	property	no	Leietaker kategorier
.tenant_claim	property	no	Leietaker krav
tenant claim	property	no	Leietaker krav
tenant claim categories	property	no	Leietaker kategorier klager
tenant claim is not issued for project in voucher %1	property	no	Krav mot leietaker er ikke registert for prosjekt i bilag %1
tenant global categories	property	no	Leietaker globale kategorier
tenant_id	property	no	Leietaker-id
tenant is not defined, claim not issued	property	no	Leietaker er ikke definert - krav ikke lagret
tenant phone	property	no	Leietaker tlf
tender chapter	property	no	Beskrivelse kapittel
termination date	property	no	Oppsigelse dato
test cron	property	no	Test cron
text	property	no	Tekst
text_view	property	no	Vis tekst
ticket %1 has been saved	property	no	Melding %1 er lagret
that vendor id is not valid !	property	no	Denne leverandør-id er ikke gyldig
the address to which this order will be sendt	property	no	Adressen ordren vil bli sendt til
the apartment is private. if the apartment should be public, uncheck this box	property	no	Leiligheter er ikke
the apartment is public. if the apartment should be private, check this box	property	no	leiligheten er merket public. Dersom den skulle være privat - kryss av denne boksen
the building is private. if the building should be public, uncheck this box	property	no	Bygningen er merket privat. Dersom den skulle være public - fjern krysset i denne boksen
the building is public. if the building should be private, check this box	property	no	Bygningen er merket public. Dersom den skulle være privat - kryss av denne boksen
the entrance is private. if the entrance should be public, uncheck this box	property	no	Inngangen er merket privat. Dersom den skulle være public - fjern krysset i denne boksen
the entrance is public. if the entrance should be private, check this box	property	no	Inngangen er merket public. Dersom den skulle være privat - kryss av denne boksen
the file is already imported !	property	no	Fila er allerede importert
the mail server returned	property	no	E-post serveren returnerte
the number of %1 hour is added!	property	no	%1 poster er lagt til
the order will also be sent to this one	property	no	Bestillinga blir også sendt til denne
the project %1 does not exist	property	no	Prosjekt %1 finnes ikke
the project has not been saved	property	no	Prosjektet er ikke lagret
the property is private. if the property should be public, uncheck this box	property	no	Eiendommen er merket privat. Dersom den skulle være public - fjern krysset i denne boksen
the property is public. if the property should be private, check this box	property	no	Eiendommen er merket public. Dersom den skulle være privat - kryss av denne boksen
the recipient did not get the email:	property	no	Addressaten fikk ikke epost:
the total amount to claim	property	no	Total sum for krav
the workorder has not been saved	property	no	Bestillingen er ikke lagret
this account is not valid:	property	no	Denne kontoen er ikke gyldig:
this activity code is already registered!	property	no	Denne aktivitetskoden er allrerede registert
this agreement code is already registered!	property	no	Denne avtalekoden er allrerede registert
this agreement group code is already registered!	property	no	Denne avtale-gruppe-koden er allerede registert!
this apartment_id id does not exist!	property	no	Denne leilighet-id eksisterer ikke
this apartment is already registered!	property	no	Denne leiligheten er allrerede registert
this attribute turn up as disabled in the form	property	no	Denne egenskapen vises som inaktivt i skjemaet
this building id does not exist!	property	no	Denne bygnings-id eksisterer ikke
this building_id id does not exist!	property	no	Denne bygnings-id eksisterer ikke
this building is already registered!	property	no	denne bygningen er allrerede registert
this dim a is not valid:	property	no	Dim A er ikke gyldig:
this dim d is not valid:	property	no	Denne dim 6 er ikke gyldig:
dim d is mandatory	property	no	Dim 6 er obligatorisk
this entrance id does not exist!	property	no	Denne inngang-id eksisterer ikke
this entrance_id id does not exist!	property	no	Denne bygnings-id eksisterer ikke
this entrance is already registered!	property	no	Denne inngangen er allrerede registert
this equipment id already exists!	property	no	Denne utstyrskoden er allrerede registert
this equipment_id id does not exist!	property	no	Denne bygnings-id eksisterer ikke
this file already exists !	property	no	denne filen finnes allrerede
this location id does not exist!	property	no	Denne lokaliserings-id finnes ikke!
this location is already registered!	property	no	Denne lokaliseringen er allerede registert!
this location parent id does not exist!	property	no	Denne lokaliserings-forelder-id finnes ikke!
this meter id is already registered!	property	no	Denne måler-id er allrerede registert
this order is sent by %1 on behalf of %2	property	no	Denne bestillinga er bestilt av %1 på vegne av %2
this property id does not exist!	property	no	Denne eiendoms-id eksisterer ikke
this user has not defined an email address !	property	no	Denne brukeren har ikke definert en epost adresse !
this vendor is already registered for this activity	property	no	Denne leverandøren er allerede registert for denne aktiviteten
ticket	common	no	Melding
.ticket	property	no	Melding
ticket %1 has been deleted	property	no	Melding %1 er slettet
ticket categories	property	no	Meldinger kategorier
ticket has been saved	property	no	Melding er lagret
last	property	no	Siste
ticket has been updated	property	no	melding er oppdatert
ticket id	property	no	Melding-id
time and budget	property	no	Budsjett og frister
times	property	no	Tider
timestampopened	property	no	Startet
timing	property	no	Timing
title	property	no	Tittel
to	property	no	Til
to alter the priority key	property	no	Endre nøkkel
to date	property	no	Til dato
todays date, eg. %1	property	no	Dagens dato, %1
tolerance	property	no	Toleranse
total cost	property	no	Total
total sum	property	no	Total sum
tracking helpdesk	property	no	Sporing helpdesk hovedliste
transfer	property	no	Overfør
transfer time	property	no	Overført
true	property	no	True
tts	property	no	Melding
type	property	no	Type
type invoice ii	property	no	Type
type of changes	property	no	Type av endring
uncheck to debug the result	property	no	Ta vekk avkryssning for debugging
unit	property	no	Enhet
up	property	no	Opp
update	property	no	Oppdater
update a single entry by passing the fields.	property	no	Oppdater en enkelt post
update email	property	no	Oppdater e-post
update file	property	no	Oppdater fil
update location	property	no	Oppdater lokasjon
update project	property	no	Oppdater prosjekt
update selected investments	property	no	Oppdater avmerkede investeringer
update subject	property	no	Oppdater overskrift
update the category to not active based on if there is only nonactive apartments	property	no	Oppdater kategori til utgått basert på om det bare finnes utgåtte leiligheter
update the not active category for locations	property	no	Oppdater kategori til utgått for lokaliseringer
url	property	no	URL
used in	property	no	Brukt i
user	property	no	Bruker
user contact info	property	no	Bruker kontaktinfo
user gratification	property	no	Bruker tilfredsstillelse
username / group	property	no	Brukernavn / Gruppe
users	property	no	Brukere
users email is updated	property	no	Brukers epost er oppdatert
users phone is updated	property	no	Brukers telefon er oppdatert
value	property	no	Verdi
values	property	no	Verdier
varchar	property	no	varchar
vendor	property	no	Leverandør
vendor attributes	property	no	Leverandør egenskaper
vendor categories	property	no	Leverandør kategorier
vendor global categories	property	no	Globale leverandør kategorier
vendor has been added	property	no	Leverandør er lagt til
vendor id	property	no	Lev-id
vendor is not defined in order %1	property	no	Leverandør er ikke definert i ordre %1
vendor name	property	no	Leverandørnavn
vendor reminder	property	no	Leverandørpurringer
version	property	no	versjon
view apartment	property	no	Vis leilighet
view building	property	no	Vis bygning
view document	property	no	Vis dokument
view documents for this location/entity	property	no	Vis dokumenter for denne lokaliseringen / entiteten
view documents for this location/equipment	property	no	Vis dokument for denne lokaliseringen/utstyret
view/edit the history	property	no	Vis/oppdater historikk
view edit the prize for this activity	property	no	Vis/oppdater pris for denne aktiviteten
view entrance	property	no	Vis inngang
view equipment	property	no	Vis utstyr
view error log	common	no	Vis feilmeldings log
view gab	property	no	Vis grunneiendom
view gab detail	property	no	Vis grunneiendom-detaljer
view gab-info	property	no	Vis grunneiendom
view information about the document	property	no	Vis informasjon om dokumentet
view investment	property	no	Vis investering
view map	property	no	Vis kart
view meter	property	no	Vise måler
view or edit prizing history of this element	property	no	Vis eller oppdater prishistorikk for dette elementet
view project	property	no	Vis prosjekt
view property	property	no	Vis eiendom
view request	property	no	Vis behov
view template detail	property	no	Vis mal-detaljer
view tender	property	no	Vis beskrivelse
view the apartment	property	no	Vis leilighet
view the attrib	property	no	Vis egenskap
view the budget account	property	no	Vis kostnadsart
view the building	property	no	Vis bygning
view the category	property	no	Vis kategorien
view the claim	property	no	Vis kravet
view the complete workorder	property	no	Vis den komplette bestilling
view the complete workorder as a tender for bidding	property	no	Vis den komplette bestilling som beskrivelse
view the document	property	no	Vis dokument
view the entity	property	no	Vis entiteten
view the entrance	property	no	Vis inngang
view the equipment	property	no	Vis utstyr
view the gab	property	no	Vis grunneiendom
view the location	property	no	Vis lokalisering
view the meter	property	no	Vis måler
view the method	property	no	Vis metoden
view the part of town	property	no	Vis bydelen
view the project	property	no	Vis prosjekt
view the property	property	no	Vis eiendom
view the request	property	no	Vis behov
view the standard	property	no	Vis standard
view the template	property	no	Vis mal
view the ticket	property	no	Vis melding
view the vendor(s) for this activity	property	no	Vis leverandør(er) for dette elementet
view the workorder	property	no	Vis arberidsordre
view this entity	property	no	Vis denne entiteten
add a project	property	en	add a project
view ticket detail	property	no	Vis meldingsdetalj
view workorder	property	no	Vis bestilling
voucher	property	no	Bilag
voucher date	property	no	Bilagsdato
voucher id	property	no	Bilagsnr.
voucher id already taken	property	no	Bilagsnummer er allerede brukt
voucher is updated	property	no	Bilag er oppdatert
voucher is updated:	property	no	Bilag er oppdatert:
voucher period is updated	property	no	bilagsperiode er oppdatert
voucher process code	property	no	Årsakskode
voucher process log	property	no	Fakturalogg
warning: the record has to be saved in order to plan an event	property	no	NB! posten må lagres før hendelse kan planlegges
warning: show cost estimate	property	no	Advarsel: kostnadsestimat blir synlig for leverandør
w_cost	property	no	Arbeidskostnad
weekly	property	no	Ukentlig
weight for prioritising	property	no	Vekting av prioritering
what is the current status of this document ?	property	no	Hva er status for dette dokumentet ?
what is the current status of this equipment ?	property	no	Hva er status for dette utstyret ?
what is the current status of this project ?	property	no	Hva er status for dette prosjektet ?
what is the current status of this request ?	property	no	Hva er status for dette behovet ?
what is the current status of this workorder ?	property	no	Hva er status for denne bestillingen ?
when	property	no	Når
where	property	no	Hvor
where to deliver the key	property	no	Nøkler leveres
where to fetch the key	property	no	Nøkler hentes
where to pick up the key	property	no	Nøkler hentes
which entity type is to show up in location forms	property	no	Hvilke entitet-type skal vises i lokaliseringsskjema
work:____________	property	no	Arbeid:____________
workorder	property	no	Bestilling
workorder %1 has been edited	property	no	Bestilling %1 er oppdatert
workorder %1 has been saved	property	no	Bestilling %1 er lagret
workorder %1 needs approval	property	no	Bestilling %1 venter på godkjenning
workorder detail categories	property	no	Detaljering av bestilling
workorder end date	property	no	Sluttdato
workorder entry date	property	no	Registreringsdato
workorder id	property	no	Bestilling
workorder %1 is sent by email to %2	property	no	Bestilling %1 er sendt pr e-post til %2
workorders status	property	no	Status bestilling
workorder start date	property	no	Start dato for bestilling
workorder status	property	no	Status
workorder template	property	no	Bestillings mal
workorder title	property	no	Bestillings tittel
workorder user	property	no	Bruker
write off	property	no	Avskrivning
write off period	property	no	Avskrivningsperiode
year	property	no	År
yearly	property	no	Årlig
you are not approved for this task	property	no	Du mangler rettigheter for denne oppgaven
you are not approved for this dimb: %1	property	no	Du mangler rettigheter for dette ansvarsstedet: %1
you have entered an invalid end date !	property	no	Du har angitt en ugyldig slutt dato
you have entered an invalid start date !	property	no	Du har angitt en ugyldig start dato
you have no edit right for this project	property	no	Du har ikke redigeringsrettigheter for dette prosjektet
you do not have permission to approve this order	property	no	Du har ikke rettighteter for a godkjenne bestillingen
you have to select a budget responsible for this invoice in order to add the invoice	property	no	Du må velge en anviser for å kunne legge til en faktura
you have to select a budget responsible for this invoice in order to make the import	property	no	Du må velge en anviser for å importere
you have to select the conversion for this import	property	no	Du må velge importformat
you have to select type of invoice	property	no	Du må velge art
your message could not be sent!	property	no	Din melding kunne ikke sendes
your message could not be sent by mail!	property	no	Din melding kunne ikke sendes med epost!
usertype	common	no	Brukertype
tenant	common	no	Leietaker
internal	common	no	Intern
in progress date	property	no	Påbegynt dato
inherit location	property	no	Arve lokalisering til bestilling
delivered date	property	no	Utført dato
checklist	property	no	Sjekkliste
closed date	property	no	Avsluttet dato
cost categories	property	no	Kostnadskategori
negative value for budget	property	no	Negativ verdi for kostnadsestimat
no symptoms	property	no	Ingen avvik
not a valid category	property	no	Ugyldig kategori
not a valid budget account	property	no	Ugyldig kostnadsart
minor symptoms	property	no	Ikke vesentlig avvik
medium symptoms	property	no	Vesentlig avvik
serious symptoms	property	no	Stort eller vesentlig avvik
condition not assessed	property	no	Ikke udersøkt. Mulig risiko
low probability	property	no	Liten sannsynlighet
medium probability	property	no	Middels sannsynlighet
modified date	property	no	Oppdatert dato
high probability	property	no	Stor sannsynlighet
weight	property	no	Vekt
risk	property	no	Risiko
elements_pr_page	property	no	Poster pr. side
shows_from	property	no	Viser
of_total	property	no	av
fictive	property	no	Fiktiv
first	property	no	Første
previous	property	no	Forrige
next	property	no	Neste
action year	property	no	Tiltaksår
action cost overview	property	no	Kostnadsoversikt
request-id condition	property	no	Fra tilstandsanalyse-id
apartment	property	en	Apartment
request coordinator	property	no	Byggforvalter
request action mouseover title	property	no	Beskriv tiltak
request action title	property	no	Tiltaksbeskrivelse
request condition mouseover description	property	no	Beskriv tilstand
request condition description	property	no	Tilstandsbeskrivelse
cost operation	property	no	Andel kostnad D/V i kr.
cost investment	property	no	Andel investering i kr.
grant category	property	no	Tilskuddskategori
what	property	no	Hva
fictive periodization	property	no	Periodiser løpende ut året
tender deadline	property	no	Tilbudsfrist
tender received	property	no	Mottatt tilbud
inspection on completion	property	no	Ferdigbefaring
end date delay	property	no	Forsinket utførelse
tender delay	property	no	Forsinket tilbud
enable bulk	common	no	Aktiver bulk
enable controller	common	no	Aktiver kontroll
entity group	common	no	Registergruppe
modified on	common	no	Oppdatert
property type	property	no	Type
hjemmel	common	no	Hjemmel
record has been edited	common	no	Data er redigert
external project	property	no	Eksternt prosjekt
unspsc code	common	no	UNSPSC-kode
order received	property	no	Varemottak
receive order	property	no	Motta vare
check date type	property	no	Dato filter type
no date	property	no	Ingen dato
please select an external project!	property	no	Velg et tilnyttet eksternt prosjekt
mine roles	property	no	Mine roller
account type	property	no	Rolletype
update ticket	property	no	Oppdater melding
make relation	property	no	Opprett kopling
request for approval	property	no	Anmodning om godkjenning
approval from %1 is required for order %2	property	no	Godkjenning fra %1 er påkrevd for ordre %2
missing recipient for order %1	property	no	Mangler mottaker for ordre %1
export	property	no	Eksport
simplified	property	no	Forenklet
implicitly from project	property	no	Implisitt fra prosjekt
generic import	property	no	Generisk import
import components	property	no	Importer FDV-dokumentasjon
relations	property	no	Koblinger
components	property	no	Komponenter
locations	property	no	Lokasjoner
choose profile	property	no	Velg profil
attributes template	property	no	Mal for feltmapping
preview	property	no	Forhåndsvis
start upload	property	no	Start opplasting
cancel upload	property	no	Kanseller opplasting
add files	property	no	Legg til filer
start import	property	no	Start import
choose attribute	property	no	Velg datafelt
choose attribute name for component id	property	no	Velg datafelt som skal representere komponent Id
number files	property	no	Antall filer
without components	property	no	Uten komponenter
with components	property	no	Med komponenter
uncompressed	property	no	Ukomprimert
compressed	property	no	Komprimert
file name	property	no	Filnavn
row	property	no	Rad
download preview components	property	no	Last ned forhåndsvisning av komponenter
new attributes	property	no	Nye egenskaper
new categories	property	no	Nye kategorier
save profile	property	no	Lagre profil
columns and attributes	property	no	Kolonner og egenskaper
attribute name for component id	property	no	Egenskapnavn for komponent id
category template	property	no	Kategori mal
profile	property	no	Profil
new attribute	property	no	Ny egenskap
report	property	no	Rapport
canceled	property	no	Kansellert
substitute	property	no	Vikar
set substitute	property	no	Sett vikar
circle reference	property	no	Sirkelreferanse
reports	property	no	Rapporter
report generator	property	no	Rapportgenerator
datasets	property	no	Datasett
dataset	property	no	Datasett
dataset name	property	no	Navn på datasett
group by	property	no	Gruppering
sort by	property	no	Sortering
count / sum	property	no	Telling og summering
criteria	property	no	Kriterier
operator	property	no	Operator
restricted value	property	no	Felt for avgrensing
conector	property	no	Koblingstype
report name	property	no	Navn på rapport
choose	property	no	Velg
unselect	property	no	Nullstill
get columns	property	no	Hent kolonner
and	property	no	Og
or	property	no	Eller
show	property	no	Vis
access not permitted	property	no	Ingen tilgang
activate	property	no	aktiver
add new document	property	no	Legg til nytt dokument
add report	property	no	Legg til rapport
all types	property	no	Alle typer
amount to transfer	property	no	Beløp for overføring
app	property	no	App
budget for selected year	property	no	Budsjett for valgt år
budget info	property	no	Budsjett informasjon
budget year	property	no	Budsjettår
building part has been updated	property	no	Bygningsdel er oppdatert
check to attach file	property	no	Merk av for å legge ved fil
check to delete year	property	no	Merk av for å slette år
choose dataset	property	no	Velg datasett
claim_id	property	no	Krav-id
click this button to start the export	property	no	Klikk for å starte eksporten
click to delete file	property	no	Klikk for å slette fil
click to select dimb	property	no	Klikk for å velge ansvarssted
click to select external project	property	no	Klikk for å velge eksternt prosjekt
condition survey categories	property	no	Kategorier for tilstandsanalyse
condition survey status	property	no	Status for tilstandsanalyse
convert to eav	property	no	Konverter til EAV
created	property	no	Opprettet
custom config	property	no	Tilpasset konfigurasjon
delete imported records	property	no	Slett importerte verdier
delete the ticket	property	no	Slett melding
download jasperreport %1 definition	property	no	Last ned jasperreport %1 definisjonen
edit serie	property	no	Endre serie
edit the jasper entry	property	no	Endre jasper oppføringen
enter a remark to add to the history of the order	property	no	Skriv en merknad som blir lagt til historikken
enter a value for:	property	no	Angi en verdi for:
filetype	property	no	Filtype
format	property	no	Format
gab info	property	no	Matrikkel informasjon
generic document	property	no	Generisk dokument
grand total	property	no	Totalsum
has been deleted	property	no	Er slettet
leave the record untouched and return to the list	property	no	La posten være urørt, og returnere til listen
list contacts	property	no	List kontakter
list pictures	property	no	List bilder
list report definitions	property	no	List rapportdefinisjon
location contact	property	no	Lokasjonskontakt
location has been updated	property	no	Lokasjon er oppdatert
mandatory actual cost	property	no	Obligatorisk faktisk kostnad
mandatory project group	property	no	Obligatorisk prosjektgruppe
mine documents	property	no	Mine dokumenter
mine tasks	property	no	Mine oppgaver
mobile	property	no	Mobil
no filetype	property	no	Filtype ikke valgt
number of reports	property	no	Antall rapporter
open vendor in new window	property	no	Åpne leverandør i nytt vindu
parent	property	no	Forelder
path	property	no	Sti
please - enter a invoice num!	property	no	Angi et bilagsnummer
please - enter an integer for order!	property	no	Angi et heltall for bestiling
please - select write off period or enter new number of period !	property	no	Velg avskrivningsperiode, og antall perioder
please enter a category !	property	no	Angi en kategori
please enter a description !	property	no	Angi en beskrivelse
please enter a multiplier !	property	no	Angi en multiplikator
please enter a status !	property	no	Angi en status
print the ticket	property	no	Skriv ut melding
project attributes	property	no	Prosjektegenskaper
project status	property	no	Prosjektstatus
projekt	property	no	Prosjekt
reporting	property	no	Rapportering
request categories	property	no	Behovskategorier
response template	property	no	Respons mal
s_agreement	property	no	Serviceavtale
save the record and return to the list	property	no	Lagre posten og returner til listen
select a group	property	no	Velg en gruppe
select an conector for:	property	no	Velg en kobling for:
select an operator for:	property	no	Velg en operator for:
select at least one column	property	no	Velg minst en kolonne
select at least one count/sum operation	property	no	Velg minst en telle/sum operasjon
select sub category	property	no	Velg underkategori
select the estimated start date for the project	property	no	Angi estimert startdato for prosjektet
select the priority the selection belongs to	property	no	Angi prioritering
select type	property	no	Velg type
send this workorder to vendor	property	no	Send denne ordren til leverandør
set responsible unit	property	no	Angi ansvarlig enhet
size	property	no	Størrelse
status filter	property	no	Status filter
status new	property	no	Status ny
street	property	no	Gate
target	property	no	Mål
target project	property	no	Mål-prosjekt
tenant category	property	no	Leietakerkategori
ticket attributes	property	no	Meldingsegenskaper
ticket config	property	no	Meldingskonfigurasjon
ticket priority	property	no	Meldingsprioritet
ticket status	property	no	Meldingsstatus
vendor category	property	no	Leverandør kategori
workorder recalculate actual cost	property	no	Rekalkuler faktisk kostnad for bestillinger
none selected	property	no	Ingen valgt
direction	property	no	Retning
directory	property	no	Katalog
select template	property	no	Velg mal
select parent	property	no	Velg forelder
attribute groups	property	no	Egenskapgrupper
sms text	property	no	SMS-tekst
character left	property	no	Resterende tegn
severity	property	no	Alvorlighetsgrad
location exception	property	no	OBS varsel
severity category	property	no	Kategori for alvorlighetsgrad
severity category text	property	no	Tekster for kategori for alvorlighetsgrad
reference	property	no	Referanse
alert vendor	property	no	Varsle leverandør
important information	property	no	Viktig informasjon
category content	property	no	Tekster for kategori
order deadline	property	no	Frist for utførelse
request location level	property	no	Behov: lokasjonsnivå
please update <a href="%1">your email address here</a>	property	no	Venligst oppdatert din <a href="%1">epost-adresse her</a>
clear contact	property	no	Fjern kontakperson
delete claim	property	en	delete claim
deadline for start	property	no	Frist for oppstart
deadline for execution	property	no	Frist for ferdigstillelse
outside contract	property	no	Utenfor rammeavtale
refund	property	no	Refusjon
both	property	no	Begge
condition survey location level	property	no	Tilstandsanalyse lokasjonsnivå
condition survey import category	property	no	Tilstandsanalyse importkategori
show overdue projects on main screen	property	no	Vis prosjekter over tiden på hovedskjermen
link to projects you are assigned to	property	no	Knytt til prosjekter du er tildelt
show open tenant claims on main screen	property	no	Vis åpne leietaker krav på hovedskjermen
link to claims you are assigned to	property	no	Knytt til krav du er tildelt
the default group to assign a ticket in helpdesk-submodule	property	no	Standardgruppen for å tildele en billett i helpdesk-submodule
budget account as listbox	property	no	Budsjettkonto som listeboks
the input type for budget account	property	no	Input typen for budsjettkonto
default unspsc code	property	no	Standard unspsc kode
contact block 1	property	no	Kontakt blokk 1
contact block 2	property	no	Kontakt blokk 2
check missing project budget	property	no	Sjekk manglende prosjekt budsjett
request condition_type	property	no	Forespør betingelsestype
pending action type	property	no	Venter på handlingstype
use acl for document types. (not implementet)	property	no	Bruk acl for dokumenttyper . (Ikke implementert)
org unit id	property	no	Organsisasjonsnummer
order footer header	property	no	Overskrift for bestillings-avslutnings-tekst
order footer	property	no	bestillings-avslutnings-tekst
order logo	property	no	Logo på bestilling
order logo width	property	no	Bredde for logo på bestilling
sms client order notice	property	no	SMS-varsling til kunde
dimb responsible 1	property	no	kostnadsstedsansvarlig 1
dimb responsible 2	property	no	kostnadsstedsansvarlig 2
invoicehandler	property	no	Fakturahåndterer
invoice acl	property	no	Kontroll av fakturarettigheter
project status on approval	property	no	Prosjektstatus ved godkjenning
project status on last order closed	property	no	Prosjektstatus ved siste ordre lukket
workorder status on approval	property	no	Arbeidsordre status ved godkjenning
ticket status on approval	property	no	Meldingsstatus ved godkjenning
approval amount limit	property	no	Godkjenningsgrense
approval level	property	no	Godkjenningsnivå
workorder status on ordered	property	no	Arbeidsordre status ved bestilling
request status on project hookup	property	no	Behovsstatus ved prosjektoppkobling
request status on ticket hookup	property	no	Behovsstatus ved meldingsoppkobling
workorder status that are to be set when invoice is processed	property	no	Arbeidsordre status som skal settes når fakturaen er behandlet
workorder reopen status that are to be set when invoice is processed	property	no	Arbeidsordre gjenåpne-status som skal settes når fakturaen er behandlet
require building part at workorder	property	no	Krever bygningsdel på arbeidsordre
require vendor at workorder	property	no	Krever leverandør på arbeidsordre
delay operation workorder end date	property	no	Utsett sluttdato for driftsarbeidsordre
last day in year	property	no	siste dag i året
enable unspsc kode	property	no	Aktiver unspsc kode
enable order service id	property	no	Aktiver dimensjonen tjeneste i bestilling
default municipal number	property	no	Standard kommunenummer
tax	property	no	MVA
enter the location of files url	property	no	Skriv inn adressen til filene (url)
path to external files for use with location	property	no	Sti til eksterne filer for bruk med plassering
on windows use	property	no	På Windows bruk
max recursive level at external files	property	no	Maks rekursivt nivå på eksterne filer
filter at level at external files	property	no	Filter på nivå ved eksterne filer
enter map url	property	no	Skriv inn kart url
enter gab location level	property	no	Skriv inn matrikkel-lokasjonsnivå
default value is	property	no	Standardverdi er
enter gab url	property	no	Skriv inn matrikkel url
gab url paramtres	property	no	Matrikkel url parametre
suppress old tenant	property	no	Deaktiver leietaker
show billable hours	property	no	Vis egne timer
open translates to	property	no	Åpen oversetter til
tts assign group candidates	property	no	Tts tildele gruppekandidater
tts disable assign to user on add	property	no	Tts deaktivere tilordne til bruker på legg til
tts simplified group	property	no	Tts forenklet gruppe
tts simplified categories	property	no	Tts forenklet kategorier
mail notification	property	no	E-post varsling
owner notification project	property	no	Prosjekteier varsles
owner notification tts	property	no	Eier varsling tts
assigned notification tts	property	no	Tildelt varsling tts
group notification tts	property	no	Gruppe varsling tts
tts file upload	property	no	Tts filopplasting
mandatory title tts	property	no	Obligatorisk tittel for meldinger (tts)
tts finnish date	property	no	Tts ferdig dato
tts order contact at location	property	no	Tts bestille kontakt på stedet
send response tts	property	no	Sende svar tts
project suppress meter	property	no	Prosjekt: deaktiver måler
project suppress coordination	property	no	Prosjekt: deaktiver koordinering
project optional category	property	no	Prosjekt: valgfri kategori
request show dates	property	no	Behov: vis datoer
add a method	property	en	add a method
request coordinator text	property	no	Behov: koordinatortekst
meter table	property	no	Målertabell
delete column	property	en	Delete column
receive workorder status by sms	property	no	Motta arbeidsordre status via SMS
use acl for accessing location based information	property	no	Bruk acl for å få tilgang til plasseringbasert informasjon
bypass acl for accessing tickets	property	no	Bypass acl for å få tilgang til meldinger
bypass acl for accessing entities	property	no	Bypass acl for å få tilgang til generiske entiteter
use acl for helpdesk categories	property	no	Bruk acl for meldingskategorier
use location at workorder	property	no	Bruk lokasjon på arbeidsordre
budget at project level	property	no	Budsjett på prosjektnivå
common budget account at project level	property	no	Felles budsjettkonto på prosjektnivå
update project budget from order	property	no	Oppdater prosjektbudsjett fra ordre
disallow multiple condition types at demands	property	no	Ikke tillat flere tilstandstyper på behov
list location level	property	no	List lokasjonsnivå
ntlm alternative host	property	no	Alternativ serverhost ved ntlm (filopplasting)
uploader filetypes	property	no	Opplastingsfiltyper
filter buildingpart	property	no	Filter for bygningsdel
initial status that are to be set when condition survey are imported	property	no	Innledende status som skal settes når tilstandsanalysen blir importert
hidden status that are to be set when condition survey are imported	property	no	Skjulte statuser som skal settes når tilstandsanalyse blir importert
obsolete status that are to be set for old records when condition survey are imported	property	no	Foreldet status som skal settes for gamle poster når tilstandsundersøkelsen blir importert
no	property	no	NEI
yes	property	no	JA
none	property	no	Ingen
location_id	property	no	Lokasjons ID
short description	property	no	Kort beskrivelse
do you really want to convert to eav	property	no	Er du sikker på at du vil konvertere til eav?
appname	property	no	modulnavn
help	property	no	Hjelp
do you really want to recalculate all actual cost for all workorders	property	no	Vil du omregne alle faktiske kostnader for alle arbeidsordre?
pre commit	property	no	Før lagring til databasen
client side	property	no	Klientside
list custom attribute	property	no	Liste over egendefinerte egenskaper
client-side	property	no	Klientside
default ticket categories	property	no	Default meldingskategori
default assign to tts	property	no	Default tildele til, meldinger
default group tts	property	no	Default gruppe for meldinger
dimb role	property	no	DimB Rolle
process code	property	no	Årsakskode
add timer	property	no	Legg til tidsplan
migrate	property	no	Migrer
list available domains	property	no	Liste over domener
db_host	property	no	Db_server
db_name	property	no	Db_navn
db_type	property	no	Db_type
distribute	property	no	Distribuer
distribute year	property	no	Distribuerings år
settings	property	no	Innstillinger
ask for workorder approval by email	property	no	Be om betillingsgodkjenning via e-post
ask for project approval by email	property	no	Be om prosjektgodkjenning via e-post
tts default interface	property	no	TTS default interface
fm settings	property	no	Innstillinger for eiendomsforvaltning
comma separated email addresses to be notified about tenant claim	property	no	Kommmaseparert liste over hvem som skal ha e-post om leietakerkrav
%1 buildings has been updated to not active of %2 already not active	property	en	%1 Buildings has been updated to not active of %2 already not active
%1 entrances has been updated to not active of %2 already not active	property	en	%1 Entrances has been updated to not active of %2 already not active
%1 entries is added!	property	en	%1 entries is added!
%1 entries is updated!	property	en	%1 entries is updated!
%1 properties has been updated to not active of %2 already not active	property	en	%1 Properties has been updated to not active of %2 already not active
abstract	property	en	Abstract
access	property	en	access
access error	property	en	Access error
acl_locastion is missing	property	en	acl_locastion is missing
acquisition date	property	en	Acquisition date
action	property	en	Action
active	property	en	Active
activities	property	en	Activities
activity	property	en	Activity
activity code	property	en	Activity code
activity has been edited	property	en	Activity has been edited
activity has been saved	property	en	Activity has been saved
activity id	property	en	Activity ID
activity num	property	en	Activity Num
actor	property	en	actor
actual cost	property	en	Actual cost
add	property	en	Add
add a apartment	property	en	add a apartment
add a budget account	property	en	add a budget account
add a building	property	en	add a building
add a category	property	en	add a category
add a claim	property	en	add a claim
add a custom query	property	en	add a custom query
add a custom_function	property	en	add a custom_function
add a deviation	property	en	add a deviation
add a document	property	en	add a document
add a entity	property	en	add a entity
add a entrance	property	en	add a entrance
add a equipment	property	en	add a equipment
add a gab	property	en	add a gab
add a hour	property	en	add a hour
add a hour to this template	property	en	add a hour to this template
add a location	property	en	add a location
add a meter	property	en	add a meter
add a owner	property	en	add a owner
add a part of town	property	en	add a part of town
add a property	property	en	add a property
add a rental agreement	property	en	add a rental agreement
add a report	property	en	add a report
add a request	property	en	add a request
add a service agreement	property	en	add a service agreement
add a standard	property	en	add a standard
add a template	property	en	add a template
add a tenant	property	en	add a tenant
add a ticket	property	en	add a ticket
add a workorder	property	en	add a workorder
add a workorder to this project	property	en	Add a workorder to this project
add activity	property	en	add activity
add agreement	property	en	add agreement
add agreement group	property	en	add agreement group
add alarm	property	en	Add alarm
add alarm for selected user	property	en	Add alarm for selected user
add an activity	property	en	add an activity
add an actor	property	en	add an actor
add an agreement	property	en	add an agreement
add an alarm	property	en	add an alarm
add an attrib	property	en	add an attrib
add an investment	property	en	add an investment
add an invoice	property	en	add an invoice
add an item to the details	property	en	add an item to the details
add another	property	en	add another
add apartment	property	en	add apartment
add attribute	property	en	add attribute
add budget account	property	en	add budget account
add building	property	en	add building
add category	property	en	add category
add common	property	en	add common
add custom	property	en	Add custom
add custom function	property	en	add custom function
add detail	property	en	add detail
add deviation	property	en	add deviation
add document	property	en	Add document
add entity	property	en	add entity
add entrance	property	en	add entrance
add equipment	property	en	add equipment
add first value for this prizing	property	en	Add first value for this prizing
add from prizebook	property	en	Add from prizebook
add from template	property	en	Add from template
add gab	property	en	Add gab
add hour	property	en	Add hour
add investment	property	en	add investment
add invoice	property	en	Add invoice
add items from a predefined template	property	en	add items from a predefined template
add items from this vendors prizebook	property	en	add items from this vendors prizebook
add location	property	en	add location
add meter	property	en	add meter
add method	property	en	add method
add new comments	property	en	Add new comments
add project	property	en	Add Project
add property	property	en	add property
add report	property	en	add report
add request	property	en	Add request
add request for this project	property	en	Add request for this project
add selected request to project	property	en	add selected request to project
add service	property	en	add service
add single custom line	property	en	Add single custom line
add space	property	en	add space
add standard	property	en	add standard
add status	property	en	add status
add template	property	en	Add template
add the selected items	property	en	Add the selected items
add this invoice	property	en	Add this invoice
add this vendor to this activity	property	en	Add this vendor to this activity
add ticket	property	en	add ticket
add workorder	property	en	Add workorder
added	property	en	added
additional notes	property	en	Additional notes
address	property	en	Address
addressbook	property	en	addressbook
adds a new project - then a new workorder	property	en	Adds a new project - then a new workorder
adds a new workorder to an existing project	property	en	Adds a new workorder to an existing project
adds this workorders calculation as a template for later use	property	en	Adds this workorders calculation as a template for later use
admin entity	property	en	Admin entity
admin location	property	en	Admin Location
aesthetics	property	en	aesthetics
again	property	en	again
agreement	property	en	Agreement
agreement code	property	en	Agreement code
agreement group	property	en	Agreement group
agreement group code	property	en	Agreement group code
agreement group has been edited	property	en	Agreement group has been edited
agreement group has been saved	property	en	Agreement group has been saved
agreement group id	property	en	Agreement group ID
agreement has been edited	property	en	Agreement has been edited
agreement has been saved	property	en	Agreement has been saved
agreement id	property	en	Agreement ID
agreement_id	property	en	agreement_id
alarm	property	en	Alarm
alarm id	property	en	alarm id
all	property	en	All
all users	property	en	All users
altered	property	en	altered
altered by	property	en	Altered by
altering columnname or datatype  - deletes your data in this column	property	en	Altering ColumnName OR Datatype  - deletes your data in this Column
alternative - link instead of uploading a file	property	en	Alternative - link instead of uploading a file
amount	property	en	Amount
amount not entered!	property	en	amount not entered!
amount of the invoice	property	en	Amount of the invoice
an unique code for this activity	property	en	An unique code for this activity
apartment has been edited	property	en	Apartment has been edited
apartment has been saved	property	en	Apartment has been saved
apartment id	property	en	apartment id
apply	property	en	apply
apply the values	property	en	Apply the values
approval from	property	en	Approval from
approval from is updated	property	en	Approval from is updated
archive	property	en	Archive
art	property	en	Art
ask for approval	property	en	Ask for approval
assign to	property	en	Assign to
assigned from	property	en	Assigned from
assigned to	property	en	Assigned To
async	property	en	async
async method	property	en	async method
async method has been saved	property	en	async method has been saved
at location %1	property	en	at location %1
attribute	property	en	Attribute
attribute has been edited	property	en	Attribute has been edited
attribute has been saved	property	en	Attribute has been saved
attribute has not been deleted	property	en	Attribute has NOT been deleted
attribute has not been edited	property	en	Attribute has NOT been edited
attribute has not been saved	property	en	Attribute has NOT been saved
attribute id	property	en	Attribute ID
attributes	property	en	Attributes
attributes for the attrib	property	en	attributes for the attrib
attributes for the entity category	property	en	attributes for the entity category
attributes for the location type	property	en	attributes for the location type
attributes for the standard	property	en	attributes for the standard
authorities demands	property	en	authorities demands
auto tax	property	en	Auto TAX
b - responsible	property	en	B - responsible
back to admin	property	en	Back to Admin
back to calculation	property	en	Back to calculation
back to entity	property	en	back to entity
back to investment list	property	en	Back to investment list
back to list	property	en	Back to list
back to the list	property	en	Back to the list
back to the ticket list	property	en	Back to the ticket list
back to the workorder list	property	en	Back to the workorder list
base	property	en	Base
base description	property	en	Base description
bilagsnr	property	en	bilagsnr
bill per unit	property	en	Bill per unit
billable hours changed	property	en	Billable hours changed
billable rate changed	property	en	Billable rate changed
branch	property	en	branch
bruks nr	property	en	bruks nr
budget	property	en	Budget
budget account	property	en	Budget account
budget account is missing:	property	en	Budget account is missing:
budget changed	property	en	Budget changed
budget code is missing from sub invoice in :	property	en	Budget code is missing from sub invoice in :
budget cost	property	en	budget cost
budget responsible	property	en	Budget Responsible
building	property	en	Building
building common	property	en	Building common
building has been edited	property	en	Building has been edited
building has been saved	property	en	Building has been saved
building id	property	en	building id
building part	property	en	Building part
building_part	property	en	building_part
but your message could not be sent by mail!	property	en	But Your message could not be sent by mail!
calculate	property	en	calculate
calculate the workorder	property	en	calculate the workorder
calculate this workorder	property	en	calculate this workorder
calculate workorder	property	en	Calculate Workorder
calculate workorder by adding items from vendors prizebook or adding general hours	property	en	Calculate workorder by adding items from vendors prizebook or adding general hours
calculation	property	en	Calculation
cancel	property	en	Cancel
cancel the import	property	en	cancel the import
categories	property	en	Categories
categories for the entity type	property	en	categories for the entity type
categories for the location type	property	en	categories for the location type
category	property	en	Category
category changed	property	en	Category changed
category has been edited	property	en	category has been edited
category has been saved	property	en	category has been saved
category has not been saved	property	en	Category has NOT been saved
category id	property	en	category ID
change type	property	en	Change type
chapter	property	en	Chapter
char	property	en	char
character	property	en	Character
charge tenant	property	en	Charge tenant
check acivate custom function	property	en	check acivate custom function
check this to have the output to screen before import (recommended)	property	en	Check this to have the output to screen before import (recommended)
check this to notify your supervisor by email	property	en	Check this to notify your supervisor by email
check this to send a mail to your supervisor for approval	property	en	Check this to send a mail to your supervisor for approval
check to delete file	property	en	Check to delete file
check to delete this request from this project	property	en	Check to delete this request from this project
check to inherit from this location	property	en	check to inherit from this location
check to reset the query	property	en	check to reset the query
end	property	en	end
check to show this attribue in lookup forms	property	en	check to show this attribue in lookup forms
check to show this attribute in entity list	property	en	check to show this attribute in entity list
check to show this attribute in list	property	en	check to show this attribute in list
check to show this attribute in location list	property	en	check to show this attribute in location list
check to show this custom function in location list	property	en	check to show this custom function in location list
check to update the email-address for this vendor	property	en	Check to update the email-address for this vendor
choice	property	en	Choice
choose a category	property	en	Choose a category
choose an id	property	en	Choose an ID
choose charge tenant if the tenant i to pay for this project	property	en	Choose charge tenant if the tenant i to pay for this project
choose columns	property	en	Choose columns
choose copy hour to copy this hour to a new hour	property	en	Choose Copy Hour to copy this hour to a new hour
choose copy project to copy this project to a new project	property	en	Choose Copy Project to copy this project to a new project
choose copy request to copy this request to a new request	property	en	Choose Copy request to copy this request to a new request
choose copy workorder to copy this workorder to a new workorder	property	en	Choose Copy Workorder to copy this workorder to a new workorder
choose generate id to automaticly assign new id based on type-prefix	property	en	Choose Generate ID to automaticly assign new ID based on type-prefix
choose the end date for the next period	property	en	Choose the end date for the next period
choose the start date for the next period	property	en	Choose the start date for the next period
choose to send mailnotification	property	en	Choose to send mailnotification
chose if this column is nullable	property	en	Chose if this column is nullable
claim	property	en	Claim
claim id	property	en	claim id
close	property	en	Close
close order	property	en	Close order
close this window	property	en	Close this window
closed	property	en	Closed
code	property	en	Code
column %1 could not be moved	property	en	column %1 could not be moved
column %1 has been moved	property	en	column %1 has been moved
column could not be added	property	en	column could not be added
column description	property	en	Column description
column name	property	en	Column name
column name not entered!	property	en	Column name not entered!
columns	property	en	columns
columns is updated	property	en	columns is updated
common costs	property	en	common costs
condidtion degree	property	en	Condidtion degree
config	property	en	Config
configuration	property	en	Configuration
consequence	property	en	Consequence
consequential damage	property	en	consequential damage
consume	property	en	consume
contact	property	en	Contact
contact phone	property	en	Contact Phone
content	property	en	content
conversion	property	en	Conversion
coordinator	property	en	Coordinator
coordinator changed	property	en	Coordinator changed
copy hour ?	property	en	Copy hour ?
copy project ?	property	en	Copy project ?
copy request ?	property	en	Copy request ?
copy workorder ?	property	en	Copy workorder ?
correct error	property	en	Correct error
cost	property	en	Cost
cost (incl tax):	property	en	Cost (incl tax):
cost per unit	property	en	Cost per unit
could not find any location to save to!	property	en	Could not find any location to save to!
count	property	en	Count
custom	property	en	Custom
custom function	property	en	custom function
custom function file not chosen!	property	en	custom function file not chosen!
custom function has been edited	property	en	Custom function has been edited
custom function has not been saved	property	en	Custom function has NOT been saved
custom function id	property	en	Custom function ID
custom functions	property	en	Custom functions
custom functions for the entity category	property	en	custom functions for the entity category
custom queries	property	en	Custom queries
customer	property	en	Customer
custom_functions	property	en	custom_functions
data	property	en	Data
datatype	property	en	Datatype
datatype type not chosen!	property	en	Datatype type not chosen!
date	property	en	Date
date closed	property	en	Date Closed
date opened	property	en	Date Opened
date search	property	en	Date search
datetime	property	en	Datetime
day	property	en	Day
day of week (0-6, 0=sun)	property	en	Day of week (0-6, 0=Sun)
days	property	en	Days
deadline	property	en	Deadline
debug	property	en	Debug
debug output in browser	property	en	Debug output in browser
decimal	property	en	Decimal
default	property	en	default
default vendor category	property	en	default vendor category
default vendor category is updated	property	en	default vendor category is updated
delay	property	en	delay
delete	property	en	Delete
delete a single entry by passing the id.	property	en	Delete a single entry by passing the id.
delete activity	property	en	delete activity
delete agreement and all the activities associated with it!	property	en	Delete agreement and all the activities associated with it!
delete agreement group and all the activities associated with it!	property	en	Delete agreement group and all the activities associated with it!
delete apartment	property	en	delete apartment
delete async method	property	en	delete async method
delete budget account	property	en	delete budget account
delete building	property	en	delete building
delete custom	property	en	delete custom
delete document	property	en	delete document
delete entity	property	en	delete entity
delete entity type	property	en	delete entity type
delete entrance	property	en	delete entrance
delete equipment	property	en	delete equipment
delete file	property	en	Delete file
delete gab at:	property	en	delete gab at:
delete investment history element	property	en	delete investment history element
delete last entry	property	en	Delete last entry
delete last index	property	en	delete last index
delete location	property	en	delete location
delete location standard	property	en	delete location standard
delete meter	property	en	delete meter
delete owner	property	en	delete owner
delete part of town	property	en	delete part of town
delete prize-index	property	en	delete prize-index
delete project	property	en	delete project
delete property	property	en	delete property
delete report	property	en	delete report
delete request	property	en	delete request
delete template	property	en	delete template
delete tenant	property	en	delete tenant
delete the actor	property	en	delete the actor
delete the agreement	property	en	delete the agreement
delete the apartment	property	en	delete the apartment
delete the attrib	property	en	delete the attrib
delete the budget account	property	en	delete the budget account
delete the building	property	en	delete the building
delete the category	property	en	delete the category
delete the claim	property	en	delete the claim
delete the custom_function	property	en	delete the custom_function
delete the deviation	property	en	delete the deviation
delete the entity	property	en	delete the entity
delete the entrance	property	en	delete the entrance
delete the entry	property	en	Delete the entry
delete the equipment	property	en	delete the equipment
delete the gab	property	en	delete the gab
delete the item	property	en	delete the item
delete the last index	property	en	delete the last index
delete the location	property	en	delete the location
delete the meter	property	en	delete the meter
delete the method	property	en	delete the method
delete the owner	property	en	delete the owner
delete the part of town	property	en	delete the part of town
delete the project	property	en	delete the project
delete the property	property	en	delete the property
delete the request	property	en	delete the request
delete the r_agreement	property	en	delete the r_agreement
delete the standard	property	en	delete the standard
delete the s_agreement	property	en	delete the s_agreement
delete the template	property	en	delete the template
delete the tenant	property	en	delete the tenant
delete the voucher	property	en	delete the voucher
delete the workorder	property	en	delete the workorder
delete this activity	property	en	delete this activity
delete this agreement	property	en	Delete this agreement
delete this agreement group	property	en	Delete this agreement group
delete this column from the output	property	en	Delete this column from the output
delete this document	property	en	delete this document
delete this entry	property	en	Delete this entry
delete this equipment	property	en	delete this equipment
delete this gab	property	en	delete this gab
delete this hour	property	en	delete this hour
delete this item	property	en	delete this item
delete this project	property	en	delete this project
delete this report	property	en	delete this report
delete this request	property	en	delete this request
delete this value from the list of multiple choice	property	en	Delete this value from the list of multiple choice
delete this vendor from this activity	property	en	delete this vendor from this activity
delete this workorder	property	en	delete this workorder
delete ticket	property	en	delete ticket
delete value	property	en	Delete value
delete vendor activity	property	en	delete vendor activity
delete voucher	property	en	delete voucher
delete workorder	property	en	delete workorder
descr	property	en	Descr
description	property	en	Description
details	property	en	Details
deviation	property	en	deviation
deviation has been added	property	en	deviation has been added
deviation has been edited	property	en	deviation has been edited
deviation id	property	en	deviation ID
dim a	property	en	Dim A
dim a is missing	property	en	Dim A is missing
dim b	property	en	Dim B
dim d	property	en	Dim D
dima is missing from sub invoice in:	property	en	Dima is missing from sub invoice in:
directory created	property	en	directory created
disable	property	en	Disable
disabled	property	en	disabled
district	property	en	District
district_id	property	en	district_id
do not add this invoice	property	en	Do not add this invoice
do not import this invoice	property	en	Do not import this invoice
do you really want to delete this entry	property	en	do you really want to delete this entry
do you really want to update the categories	property	en	Do you really want to update the categories
do you really want to update the categories again	property	en	Do you really want to update the categories again
doc type	property	en	Doc type
document	property	en	document
document %1 has been edited	property	en	document %1 has been edited
document %1 has been saved	property	en	document %1 has been saved
document date	property	en	document date
document id	property	en	document ID
document name	property	en	Document name
documentation	property	en	Documentation
documentation for locations	property	en	Documentation for locations
documents	property	en	documents
done	property	en	Cancel
down	property	en	down
download table to your browser	property	en	Download table to your browser
download	property	en	Download
draft	property	en	DRAFT
e-mail	property	en	E-Mail
edit	property	en	Edit
edit activity	property	en	edit activity
edit agreement	property	en	edit agreement
edit agreement group	property	en	edit agreement group
edit apartment	property	en	edit apartment
edit attribute	property	en	edit attribute
edit budget account	property	en	edit budget account
edit building	property	en	edit building
edit category	property	en	edit category
edit custom function	property	en	edit custom function
edit deviation	property	en	edit deviation
edit document	property	en	Edit document
edit entity	property	en	edit entity
edit entrance	property	en	edit entrance
edit equipment	property	en	edit equipment
edit gab	property	en	Edit gab
edit hour	property	en	Edit hour
edit id	property	en	Edit ID
edit info	property	en	edit info
edit information about the document	property	en	edit information about the document
edit information about the gab	property	en	edit information about the gab
edit location	property	en	edit location
edit location config for	property	en	edit location config for
edit meter	property	en	edit meter
edit method	property	en	edit method
edit period	property	en	Edit period
edit pricing	property	en	edit pricing
edit priority key	property	en	Edit priority key
edit project	property	en	Edit Project
edit property	property	en	edit property
edit report	property	en	edit report
edit request	property	en	Edit request
edit standard	property	en	edit standard
edit status	property	en	edit status
edit template	property	en	Edit template
edit the actor	property	en	edit the actor
edit the agreement	property	en	edit the agreement
edit the agreement_group	property	en	edit the agreement_group
edit the alarm	property	en	edit the alarm
edit the apartment	property	en	edit the apartment
edit the attrib	property	en	edit the attrib
edit the budget account	property	en	edit the budget account
edit the building	property	en	edit the building
edit the category	property	en	edit the category
edit the claim	property	en	edit the claim
edit the column relation	property	en	edit the column relation
edit the custom_function	property	en	edit the custom_function
edit the deviation	property	en	edit the deviation
edit the entity	property	en	edit the entity
edit the entrance	property	en	edit the entrance
edit the equipment	property	en	edit the equipment
edit the gab	property	en	edit the gab
edit the location	property	en	edit the location
edit the meter	property	en	edit the meter
edit the method	property	en	edit the method
edit the owner	property	en	edit the owner
edit the part of town	property	en	edit the part of town
edit the pricebook	property	en	edit the pricebook
edit the project	property	en	edit the project
edit the property	property	en	edit the property
edit the report	property	en	edit the report
edit the request	property	en	edit the request
edit the r_agreement	property	en	edit the r_agreement
edit the standard	property	en	edit the standard
edit the s_agreement	property	en	edit the s_agreement
edit the template	property	en	edit the template
edit the tenant	property	en	edit the tenant
edit the workorder	property	en	edit the workorder
edit this activity	property	en	edit this activity
edit this entry	property	en	Edit this entry
edit this entry equipment	property	en	Edit this entry equipment
edit this entry project	property	en	Edit this entry project
edit this entry report	property	en	Edit this entry report
edit this entry request	property	en	Edit this entry request
edit this entry workorder	property	en	Edit this entry workorder
edit this meter	property	en	Edit this meter
edit workorder	property	en	Edit Workorder
edit/customise this hour	property	en	edit/customise this hour
email	property	en	Email
enables help message for this attribute	property	en	Enables help message for this attribute
enable	property	en	Enable
enable file upload	property	en	Enable file upload
enable history for this attribute	property	en	Enable history for this attribute
enable link from location detail	property	en	Enable link from location detail
enable start project from this category	property	en	Enable start project from this category
enabled	property	en	Enabled
end date	property	en	End date
enter a descr for the custom function	property	en	Enter a descr for the custom function
enter a description for prerequisitions for this activity - if any	property	en	Enter a description for prerequisitions for this activity - if any
enter a description of the deviation	property	en	Enter a description of the deviation
enter a description of the document	property	en	Enter a description of the document
enter a description of the equipment	property	en	Enter a description of the equipment
enter a description of the project	property	en	Enter a description of the project
enter a description of the request	property	en	Enter a description of the request
enter a description of the standard	property	en	Enter a description of the standard
enter a description of the status	property	en	Enter a description of the status
enter a description the attribute	property	en	Enter a description the attribute
enter a description the budget account	property	en	Enter a description the budget account
enter a description the category	property	en	Enter a description the category
enter a description the method	property	en	Enter a description the method
enter a description the standard	property	en	Enter a description the standard
enter a meter id !	property	en	Enter a meter ID !
enter a name for the query	property	en	Enter a name for the query
enter a name for this part of town	property	en	Enter a name for this part of town
enter a name of the standard	property	en	Enter a name of the standard
enter a new grouping for this activity if not found in the list	property	en	Enter a new grouping for this activity if not found in the list
enter a new index	property	en	Enter a new index
enter a new writeoff period if it is not in the list	property	en	Enter a new writeoff period if it is NOT in the list
enter a remark - if any	property	en	Enter a remark - if any
enter a remark for this claim	property	en	Enter a remark for this claim
enter a remark for this entity	property	en	Enter a remark for this entity
enter a remark for this owner	property	en	Enter a remark for this owner
enter a short description of the workorder	property	en	Enter a short description of the workorder
enter a short description of this template	property	en	Enter a short description of this template
enter a sql query	property	en	Enter a sql query
enter a standard prefix for the id	property	en	Enter a standard prefix for the id
enter a standard prefix for the id of the equipments	property	en	Enter a standard prefix for the id of the equipments
enter a statustext for the inputfield in forms	property	en	Enter a statustext for the inputfield in forms
enter a value for the labour cost	property	en	Enter a value for the labour cost
enter a value for the material cost	property	en	Enter a value for the material cost
enter additional remarks to the description - if any	property	en	Enter additional remarks to the description - if any
enter any persentage addition per unit	property	en	Enter any persentage addition per unit
enter any remark for this location	property	en	enter any remark for this location
enter any remarks regarding this apartment	property	en	Enter any remarks regarding this apartment
enter any remarks regarding this building	property	en	Enter any remarks regarding this building
enter any remarks regarding this entrance	property	en	Enter any remarks regarding this entrance
enter any remarks regarding this location	property	en	Enter any remarks regarding this location
enter any round sum addition per order	property	en	Enter any round sum addition per order
enter apartment id	property	en	Enter apartment ID
enter building id	property	en	Enter Building ID
enter document name	property	en	Enter document Name
enter document title	property	en	Enter document title
enter document version	property	en	Enter document version
enter entrance id	property	en	Enter entrance ID
enter equipment id	property	en	Enter equipment ID
enter invoice number	property	en	Enter Invoice Number
enter kid nr	property	en	Enter Kid nr
enter location id	property	en	Enter location ID
enter other branch if not found in the list	property	en	Enter other branch if not found in the list
enter project name	property	en	Enter Project Name
enter quantity of unit	property	en	Enter quantity of unit
enter report id	property	en	Enter report ID
enter report title	property	en	Enter Report Title
enter request title	property	en	Enter request Title
enter the abstract of the report	property	en	Enter the abstract of the report
enter the attribute id	property	en	Enter the attribute ID
enter the attribute value for this entity	property	en	Enter the attribute value for this entity
enter the budget	property	en	Enter the budget
enter the budget account	property	en	Enter the budget account
enter the category id	property	en	Enter the category ID
enter the cost per unit	property	en	Enter the cost per unit
enter the date for this reading	property	en	Enter the date for this reading
enter the default value	property	en	enter the default value
enter the description	property	en	Enter the description
enter the description for this activity	property	en	Enter the description for this activity
enter the description for this template	property	en	Enter the description for this template
enter the details of this ticket	property	en	Enter the details of this ticket
enter the email-address for this user	property	en	Enter the email-address for this user
enter the expected longevity in years	property	en	Enter the expected longevity in years
enter the floor	property	en	Enter the floor
enter the floor id	property	en	Enter the floor ID
enter the general address	property	en	Enter the general address
enter the input name for records	property	en	enter the input name for records
enter the input text for records	property	en	enter the input text for records
enter the invoice date	property	en	Enter the invoice date
enter the meter id	property	en	Enter the meter ID
enter the method id	property	en	Enter the method ID
enter the name for the column	property	en	enter the name for the column
enter the name for this location	property	en	enter the name for this location
enter the name of the apartment	property	en	Enter the name of the apartment
enter the name of the building	property	en	Enter the name of the building
enter the name of the entrance	property	en	Enter the name of the entrance
enter the name of the location	property	en	Enter the name of the location
enter the name of the meter	property	en	Enter the name of the meter
enter the name of the property	property	en	Enter the name of the property
enter the name of the tenant	property	en	Enter the name of the tenant
enter the name the template	property	en	Enter the name the template
enter the payment date or the payment delay	property	en	Enter the payment date or the payment delay
enter the power meter	property	en	Enter the power meter
enter the property id	property	en	Enter the Property ID
enter the purchase cost	property	en	Enter the purchase cost
enter the record length	property	en	enter the record length
enter the reserve	property	en	Enter the reserve
enter the scale if type is decimal	property	en	enter the scale if type is decimal
enter the search string. to show all entries, empty this field and press the submit button again	property	en	Enter the search string. To show all entries, empty this field and press the SUBMIT button again
enter the standard id	property	en	Enter the standard ID
enter the status id	property	en	Enter the status ID
enter the street number	property	en	Enter the street number
enter the subject of this ticket	property	en	Enter the subject of this ticket
enter the total cost of this activity - if not to be calculated from unit-cost	property	en	Enter the total cost of this activity - if not to be calculated from unit-cost
enter the workorder id to search by workorder - at any date	property	en	enter the Workorder ID to search by workorder - at any date
enter workorder title	property	en	Enter Workorder title
entity	property	en	entity
entity has been added	property	en	entity has been added
entity has been edited	property	en	entity has been edited
entity has not been edited	property	en	entity has NOT been edited
entity has not been saved	property	en	Entity has NOT been saved
entity id	property	en	Entity ID
entity name	property	en	Entity name
entity not chosen	property	en	Entity not chosen
entity num	property	en	entity num
entity type	property	en	Entity Type
entity type not chosen!	property	en	Entity type not chosen!
entrance	property	en	Entrance
entrance has been edited	property	en	Entrance has been edited
entrance has been saved	property	en	Entrance has been saved
entrance id	property	en	entrance id
entry date	property	en	Entry Date
equipment	property	en	Equipment
equipment %1 has been edited	property	en	equipment %1 has been edited
equipment %1 has been saved	property	en	Equipment %1 has been saved
equipment id	property	en	equipment ID
equipment type	property	en	Equipment type
equipment_id	property	en	equipment ID
event	property	en	Event
events	property	en	Events
exp date	property	en	exp date
export date	property	en	Export date
export invoice	property	en	Export invoice
export to file	property	en	Export to file
failed to copy file !	property	en	Failed to copy file !
failed to create directory	property	en	failed to create directory
failed to delete file	property	en	failed to delete file
failed to upload file !	property	en	Failed to upload file !
false	property	en	False
female	property	en	female
feste nr	property	en	Feste nr
fetch the history for this item	property	en	Fetch the history for this item
file	property	en	File
file deleted	property	en	file deleted
filename	property	en	Filename
files	property	en	files
finnish date	property	en	finnish date
first entry is added!	property	en	First entry is added!
first name	property	en	first name
first note added	property	en	First Note Added
firstname	property	en	Firstname
floor	property	en	Floor
floor common	property	en	Floor common
floor id	property	en	Floor ID
force year for period	property	en	Force year for period
fraction	property	en	fraction
from	property	en	From
from date	property	en	from date
funding	property	en	Funding
gaards nr	property	en	gaards nr
gab	property	en	gab
gab %1 has been added	property	en	gab %1 has been added
gab %1 has been edited	property	en	gab %1 has been edited
gab %1 has been updated	property	en	gab %1 has been updated
gabnr	property	en	gabnr
general address	property	en	General Address
general info	property	en	General Info
generate a project from this request	property	en	Generate a project from this request
generate id ?	property	en	Generate ID ?
generate order	property	en	Generate order
generate project	property	en	Generate project
generate request	property	en	Generate Request
group	property	en	Group
grouping	property	en	grouping
groups	property	en	Groups
help	property	en	Help
helpdesk	property	en	Helpdesk
help message	property	en	help message
highest	property	en	Highest
history	property	en	History
lastname	property	en	Lastname
history not allowed for this datatype	property	en	History not allowed for this datatype
history of this attribute	property	en	history of this attribute
hour	property	en	Hour
hour %1 has been deleted	property	en	hour %1 has been deleted
hour %1 has been edited	property	en	hour %1 has been edited
hour %1 is added!	property	en	hour %1 is added!
hour id	property	en	Hour ID
id	property	en	ID
id is updated	property	en	ID is updated
id not entered!	property	en	ID not entered!
if files can be uploaded for this category	property	en	If files can be uploaded for this category
if this entity type is to be linked to a location	property	en	If this entity type is to be linked to a location
if this entity type is to be linked to documents	property	en	If this entity type is to be linked to documents
if this entity type is to be tracket in ticket list	property	en	If this entity type is to be tracket in ticket list
if this entity type is to look up tenants	property	en	If this entity type is to look up tenants
import	property	en	Import
import from csv	property	en	Import from CSV
import invoice	property	en	Import invoice
import this invoice	property	en	Import this invoice
importance	property	en	Importance
include in location form	property	en	include in location form
include in search	property	en	Include in search
include the workorder to this claim	property	en	Include the workorder to this claim
include this entity	property	en	include this entity
index	property	en	index
index count	property	en	Index count
index date	property	en	Index date
index_count	property	en	index_count
indoor climate	property	en	indoor climate
initial category	property	en	Initial Category
initial coordinator	property	en	Initial Coordinator
initial status	property	en	Initial Status
initial value	property	en	Initial value
initials	property	en	Initials
input data for the nethod	property	en	Input data for the nethod
input name not entered!	property	en	Input name not entered!
input text	property	en	input text
input text not entered!	property	en	Input text not entered!
inputdata for the method	property	en	inputdata for the method
input_name	property	en	input_name
insert the date for the acquisition	property	en	insert the date for the acquisition
insert the date for the initial value	property	en	insert the date for the initial value
insert the value at the start-date as a positive amount	property	en	insert the value at the start-date as a positive amount
integer	property	en	Integer
investment	property	en	Investment
investment added !	property	en	Investment added !
investment history	property	en	investment history
investment id	property	en	investment id
investment id:	property	en	Investment ID:
investment value	property	en	Investment value
invoice	property	en	Invoice
invoice date	property	en	invoice date
invoice id	property	en	Invoice Id
invoice is not added!	property	en	Invoice is NOT added!
invoice number	property	en	Invoice Number
invoice transferred	property	en	Invoice transferred
invoice line text	property	en	Invoice line text
is registered	property	en	is registered
is there a demand from the authorities to correct this condition?	property	en	Is there a demand from the authorities to correct this condition?
janitor	property	en	Janitor
key deliver location	property	en	key deliver location
key fetch location	property	en	key fetch location
key responsible	property	en	key responsible
kid nr	property	en	KID nr
click this button to add a invoice	property	en	click this button to add a invoice
click this button to start the import	property	en	click this button to start the import
click this link to edit the period	property	en	click this link to edit the period
click this link to enter the list of sub-invoices	property	en	click this link to enter the list of sub-invoices
click this link to select	property	en	click this link to select
click this link to select apartment	property	en	click this link to select apartment
click this link to select budget account	property	en	click this link to select budget account
click this link to select building	property	en	click this link to select building
click this link to select customer	property	en	click this link to select customer
click this link to select entrance	property	en	click this link to select entrance
click this link to select equipment	property	en	click this link to select equipment
click this link to select owner from the addressbook	property	en	click this link to select owner from the addressbook
click this link to select property	property	en	click this link to select property
click this link to select tenant	property	en	click this link to select tenant
click this link to select vendor	property	en	click this link to select vendor
click this link to view the remark	property	en	click this link to view the remark
click this to generate a request with this information	property	en	click this to generate a request with this information
click this to generate an order with this information	property	en	click this to generate an order with this information
click this to start a report	property	en	click this to start a report
click to view file	property	en	click to view file
kommune nr	property	en	kommune nr
kreditnota	property	en	KreditNota
labour cost	property	en	Labour cost
large	property	en	Large
last index	property	en	Last index
last name	property	en	last name
leave the actor untouched and return back to the list	property	en	Leave the actor untouched and return back to the list
leave the agreement untouched and return back to the list	property	en	Leave the agreement untouched and return back to the list
leave the claim untouched and return back to the list	property	en	Leave the claim untouched and return back to the list
leave the custom untouched and return back to the list	property	en	Leave the custom untouched and return back to the list
leave the owner untouched and return back to the list	property	en	Leave the owner untouched and return back to the list
leave the part of town untouched and return back to the list	property	en	Leave the part of town untouched and return back to the list
leave the rental agreement untouched and return back to the list	property	en	Leave the rental agreement untouched and return back to the list
leave the service agreement untouched and return back to the list	property	en	Leave the service agreement untouched and return back to the list
leave the tenant untouched and return back to the list	property	en	Leave the tenant untouched and return back to the list
let this entity show up in location form	property	en	Let this entity show up in location form
link	property	en	Link
link from location	property	en	Link from location
link to the origin for this project	property	en	Link to the origin for this project
link to the origin for this report	property	en	Link to the origin for this report
link to the origin for this request	property	en	Link to the origin for this request
link to the project originatet from this request	property	en	Link to the project originatet from this request
link to the project originatet from this ticket	property	en	Link to the project originatet from this ticket
link to the report originatet from this ticket	property	en	Link to the report originatet from this ticket
link to the request for this project	property	en	Link to the request for this project
link to the request originatet from this ticket	property	en	Link to the request originatet from this ticket
link to the ticket for this project	property	en	Link to the ticket for this project
list	property	en	list
list activities per agreement	property	en	list activities per agreement
list activities per agreement_group	property	en	list activities per agreement_group
list agreement	property	en	list agreement
list agreement group	property	en	list agreement group
list apartment	property	en	list apartment
list async method	property	en	list async method
list attribute	property	en	list attribute
list budget account	property	en	list budget account
list building	property	en	list building
list config	property	en	list config
list consume	property	en	list consume
list custom function	property	en	list custom function
list deviation	property	en	list deviation
list document	property	en	list document
list entity attribute	property	en	list entity attribute
list entity custom function	property	en	list entity custom function
list entity type	property	en	list entity type
list entrance	property	en	list entrance
list equipment	property	en	list equipment
list gab	property	en	list gab
list gab detail	property	en	list gab detail
list hours	property	en	list hours
list investment	property	en	list investment
list invoice	property	en	list invoice
list location	property	en	list location
list location attribute	property	en	list location attribute
list location standard	property	en	list location standard
list meter	property	en	list meter
list paid invoice	property	en	list paid invoice
list pricebook	property	en	list pricebook
list pricebook per vendor	property	en	list pricebook per vendor
list project	property	en	list Project
list property	property	en	list property
list report	property	en	list report
list request	property	en	list request
list standard description	property	en	list standard description
list status	property	en	list status
list street	property	en	list street
list template	property	en	list template
list tenant	property	en	list tenant
list ticket	property	en	list ticket
list vendors	property	en	list vendors
list vendors per activity	property	en	list vendors per activity
list voucher	property	en	list voucher
list workorder	property	en	list workorder
listbox	property	en	ListBox
location	property	en	Location
location %1 has been edited	property	en	Location %1 has been edited
location %1 has been saved	property	en	Location %1 has been saved
location code	property	en	Location code
location config	property	en	Location Config
location form	property	en	location form
location id	property	en	location ID
location level	property	en	location level
location not chosen!	property	en	location not chosen!
location type	property	en	Location type
location type not chosen!	property	en	Location type not chosen!
longevity	property	en	Longevity
lookup	property	en	lookup
lookup tenant	property	en	lookup tenant
lowest	property	en	Lowest
mailing method is not chosen! (admin section)	property	en	Mailing method is not chosen! (admin section)
male	property	en	male
manage	property	en	Manage
manager	property	en	Manager
map	property	en	Map
mark as draft	property	en	Mark as DRAFT
mark the tender as draft	property	en	Mark the tender as DRAFT
mask	property	en	mask
material cost	property	en	Material cost
materials:__________	property	en	Materials:__________
medium	property	en	Medium
medium consequences	property	en	Medium Consequences
member of	property	en	member of
memo	property	en	Memo
meter	property	en	Meter
meter %1 has been edited	property	en	Meter %1 has been edited
meter %1 has been saved	property	en	Meter %1 has been saved
meter id	property	en	meter id
meter type	property	en	Meter type
method	property	en	Method
method has been edited	property	en	method has been edited
method id	property	en	method id
minor	property	en	Minor
minor consequences	property	en	Minor Consequences
minute	property	en	minute
minutes before the event	property	en	Minutes before the event
month	property	en	month
multiple checkbox	property	en	Multiple Checkbox
multiple radio	property	en	Multiple radio
m_cost	property	en	m_cost
name	property	en	Name
name not entered!	property	en	Name not entered!
narrow the search by dates	property	en	Narrow the search by dates
needs approval	property	en	needs approval
new	property	en	New
new grouping	property	en	New grouping
new index	property	en	New index
new note	property	en	New Note
new value	property	en	New value
new value for multiple choice	property	en	New value for multiple choice
new values	property	en	New values
next run	property	en	Next run
no	property	en	no
no access	property	en	No access
no account	property	en	No account
no additional notes	property	en	No additional notes
no branch	property	en	No branch
no category	property	en	no category
no change type	property	en	No Change type
no conversion type could be located.	property	en	No conversion type could be located.
no custom function	property	en	No custom function
no datatype	property	en	No datatype
no dim b	property	en	No Dim B
no dim d	property	en	No Dim D
no district	property	en	no district
no document type	property	en	no document type
no entity type	property	en	No entity type
no equipment type	property	en	No equipment type
no file selected	property	en	No file selected
no granting group	property	en	No granting group
no group	property	en	No group
no history	property	en	No history
no history for this record	property	en	No history for this record
no hour category	property	en	no hour category
no janitor	property	en	No janitor
no location	property	en	No location
no mailaddress is selected	property	en	No mailaddress is selected
no member	property	en	no member
no method	property	en	no method
no part of town	property	en	No part of town
no status	property	en	No status
no supervisor	property	en	No supervisor
no type	property	en	No type
no user	property	en	No user
no user selected	property	en	No user selected
no vendor	property	en	no vendor
no workorder budget	property	en	No workorder budget
none	property	en	None
none consequences	property	en	None Consequences
note	property	en	Note
nothing to do!	property	en	Nothing to do!
notify	property	en	Notify
ns3420	property	en	NS3420
ns3420 description	property	en	ns3420 description
nullable	property	en	Nullable
nullable not chosen!	property	en	Nullable not chosen!
num	property	en	Num
number	property	en	number
of	property	en	of
open	property	en	Open
open date	property	en	Open Date
opened	property	en	Opened
opened by	property	en	Opened By
open edit in new window	property	en	Open Edit in new Window
open view in new window	property	en	Open View in new Window
order	property	en	Order
order # that initiated the invoice	property	en	Order # that initiated the invoice
order id	property	en	Order ID
origin	property	en	origin
other branch	property	en	Other branch
override fraction	property	en	override fraction
override fraction of common costs	property	en	Override fraction of common costs
overview	property	en	overview
owner	property	en	owner
owner %1 has been edited	property	en	owner %1 has been edited
owner %1 has been saved	property	en	owner %1 has been saved
owner id	property	en	Owner ID
owner type	property	en	Owner type
paid	property	en	Paid
part of town	property	en	Part of town
part of town id	property	en	Part of town id
payment date	property	en	Payment Date
per agreement	property	en	Per Agreement
per cent	property	en	Per Cent
per vendor	property	en	Per Vendor
percentage addition	property	en	Percentage addition
period	property	en	Period
permission	property	en	permission
permissions	property	en	Permissions
permissions are updated!	property	en	permissions are updated!
phone	property	en	Phone
plain	property	en	plain
pleace select a location - or an equipment !	property	en	Pleace select a location - or an equipment !
please - enter an amount!	property	en	Please - enter an amount!
please - select a file to import from !	property	en	Please - select a file to import from !
please - select a import format !	property	en	Please - select a import format !
please - select a vendor!	property	en	Please - select a vendor!
please - select budget responsible!	property	en	Please - select budget responsible!
please select a budget reponsible!	property	en	Please select a budget reponsible!
please - select either payment date or number of days from invoice date !	property	en	Please - select either payment date or number of days from invoice date !
please - select type invoice!	property	en	Please - select type invoice!
please - select type order!	property	en	Please - select type order!
please - select vendor!	property	en	Please - select Vendor!
please choose a conversion type	property	en	Please choose a conversion type
please choose a conversion type from the list	property	en	Please choose a conversion type from the list
please choose a file	property	en	Please choose a file
please either select generate id or type a equipment id !	property	en	Please either select GENERATE ID or type a Equipment ID !
please enter a apartment id !	property	en	Please enter a Apartment ID !
please enter a building id !	property	en	Please enter a Building ID !
please enter a description!	property	en	Please enter a description!
please enter a entrance id !	property	en	Please enter a Entrance ID !
please enter a index !	property	en	Please enter a index !
please enter a name !	property	en	Please enter a name !
please enter a new index for calculating next value(s)!	property	en	Please enter a new index for calculating next value(s)!
please enter a project name !	property	en	Please enter a project NAME !
please enter a property id !	property	en	Please enter a Property ID !
please enter a request title !	property	en	Please enter a request TITLE !
please enter a sql query !	property	en	Please enter a sql query !
please enter a value for either material cost, labour cost or both !	property	en	Please enter a value for either material cost, labour cost or both !
please enter a workorder title !	property	en	Please enter a workorder title !
please enter an activity code !	property	en	Please enter an activity code !
please enter an agreement code !	property	en	Please enter an agreement code !
please enter an agreement group code !	property	en	Please enter an agreement group code !
please enter an integer !	property	en	Please enter an integer !
please enter an integer for order!	property	en	Please enter an integer for order!
please enter precision !	property	en	Please enter Precision !
please enter precision as integer !	property	en	Please enter precision as integer !
please enter scale as integer !	property	en	Please enter scale as integer !
please give som details !	property	en	Please give som details !
please select a branch !	property	en	Please select a branch !
please select a budget account !	property	en	Please select a budget account !
please select a building id !	property	en	Please select a Building ID !
please select a category !	property	en	Please select a category !
please select a contact !	property	en	Please select a contact !
please select a coordinator !	property	en	Please select a coordinator !
please select a date !	property	en	Please select a date !
please select a district !	property	en	Please select a district !
please select a entrance id !	property	en	Please select a Entrance ID !
please select a file to upload !	property	en	Please select a file to upload !
please select a location !	property	en	Please select a location !
please select a location %1 id !	property	en	Please select a location %1 ID !
please select a location - or an entity!	property	en	Please select a location - or an entity!
please select a period for write off !	property	en	Please select a period for write off !
please select a person or a group to handle the ticket !	property	en	Please select a person or a group to handle the ticket !
please select a person to handle the ticket !	property	en	Please select a person to handle the ticket !
please select a property id !	property	en	Please select a Property ID !
please select a status !	property	en	Please select a status !
please select a type !	property	en	Please select a type !
please select a valid project !	property	en	Please select a valid project !
please select a workorder !	property	en	Please select a workorder !
please select an agreement !	property	en	Please select an agreement !
please select an agreement_group !	property	en	Please select an agreement_group !
please select change type	property	en	Please select change type
please select entity type !	property	en	Please select entity type !
please select equipment type !	property	en	Please select equipment type !
please select report type !	property	en	Please select report type !
please select type	property	en	Please select type
please set a new index !	property	en	Please set a new index !
please set an initial value!	property	en	Please set an initial value!
please type a subject for this ticket !	property	en	Please type a subject for this ticket !
popup calendar	property	en	Popup Calendar
post	property	en	Post
power meter	property	en	Power meter
precision	property	en	Precision
preferences	property	en	preferences
prefix	property	en	prefix
presumed finnish date	property	en	presumed finnish date
pricebook	property	en	Pricebook
print view	property	en	Print view
priority	property	en	Priority
priority changed	property	en	Priority changed
priority key	property	en	Priority key
priority keys has been updated	property	en	priority keys has been updated
private	property	en	private
prizing	property	en	prizing
probability	property	en	Probability
project	property	en	Project
project budget	property	en	Project budget
project %1 has been edited	property	en	project %1 has been edited
project %1 has been saved	property	en	project %1 has been saved
project coordinator	property	en	Project coordinator
project end date	property	en	Project end date
project id	property	en	Project ID
project name	property	en	Project name
project start date	property	en	Project start date
propagate	property	en	propagate
property	common	en	Facilities Management
property has been edited	property	en	Property has been edited
property has been saved	property	en	Property has been saved
property id	property	en	property id
property name	property	en	Property Name
purchase cost	property	en	Purchase cost
quantity	property	en	Quantity
re-assigned	property	en	Re-assigned
re-opened	property	en	Re-opened
read	property	en	Read
read a list of entries.	property	en	Read a list of entries.
read a single entry by passing the id and fieldlist.	property	en	Read a single entry by passing the id and fieldlist.
read this list of methods.	property	en	Read this list of methods.
record	property	en	Record
related info	property	en	related info
remark	property	en	Remark
reminder	property	en	Reminder
rental	property	en	Rental
rental agreement	property	en	Rental agreement
rental type	property	en	rental type
report	property	en	Report
report %1 has been edited	property	en	report %1 has been edited
report %1 has been saved	property	en	report %1 has been saved
report id	property	en	report ID
repost !	property	en	Repost !
request	property	en	Request
request %1 has already been added to project %2	property	en	request %1 has already been added to project %2
request %1 has been added	property	en	request %1 has been added
request %1 has been deleted from project %2	property	en	Request %1 has been deleted from project %2
request %1 has been edited	property	en	request %1 has been edited
request %1 has been saved	property	en	request %1 has been saved
request budget	property	en	Request budget
request descr	property	en	Request descr
request end date	property	en	request end date
request entry date	property	en	Request entry date
request id	property	en	request ID
request start date	property	en	request start date
request title	property	en	Request title
requirement	property	en	Requirement
reserve	property	en	reserve
reserve remainder	property	en	reserve remainder
residential environment	property	en	residential environment
responsible	property	en	Responsible
result	property	en	result
return back to the list	property	en	return back to the list
rig addition	property	en	Rig addition
right	property	en	right
roll back	property	en	Roll back
rollback invoice	property	en	Rollback invoice
run now	property	en	Run Now
run the method now	property	en	Run the method now
safety	property	en	safety
save	property	en	save
save as template	property	en	Save as template
save the actor and return back to the list	property	en	Save the actor and return back to the list
save the agreement and return back to the list	property	en	Save the agreement and return back to the list
save the apartment	property	en	Save the apartment
save the attribute	property	en	Save the attribute
save the budget account	property	en	Save the budget account
save the building	property	en	Save the building
save the category	property	en	Save the category
save the claim and return back to the list	property	en	Save the claim and return back to the list
save the custom and return back to the list	property	en	Save the custom and return back to the list
save the custom function	property	en	Save the custom function
save the deviation	property	en	Save the deviation
save the document	property	en	Save the document
save the entity	property	en	Save the entity
save the entrance	property	en	Save the entrance
save the equipment	property	en	Save the equipment
save the gab	property	en	Save the gab
save the investment	property	en	Save the investment
save the location	property	en	Save the location
save the meter	property	en	Save the meter
save the method	property	en	Save the method
save the owner and return back to the list	property	en	Save the owner and return back to the list
save the part of town and return back to the list	property	en	Save the part of town and return back to the list
save the project	property	en	Save the project
save the property	property	en	Save the property
save the rental agreement and return back to the list	property	en	Save the rental agreement and return back to the list
save the report	property	en	Save the report
save the request	property	en	Save the request
save the service agreement and return back to the list	property	en	Save the service agreement and return back to the list
save the standard	property	en	Save the standard
save the status	property	en	Save the status
save the tenant and return back to the list	property	en	Save the tenant and return back to the list
save the ticket	property	en	Save the ticket
save the voucher	property	en	Save the voucher
save the workorder	property	en	Save the workorder
save this workorder as a template for later use	property	en	Save this workorder as a template for later use
scale	property	en	scale
schedule	property	en	Schedule
schedule the method	property	en	schedule the method
score	property	en	score
search	property	en	search
search by bruk. to show all entries, empty all fields and press the submit button again	property	en	search by bruk. To show all entries, empty all fields and press the SUBMIT button again
search by feste. to show all entries, empty all fields and press the submit button again	property	en	search by feste. To show all entries, empty all fields and press the SUBMIT button again
search by gaards nr. to show all entries, empty all fields and press the submit button again	property	en	search by gaards nr. To show all entries, empty all fields and press the SUBMIT button again
search by location_code. to show all entries, empty all fields and press the submit button again	property	en	search by location_code. To show all entries, empty all fields and press the SUBMIT button again
search by property	property	en	Search by property
search by property_id. to show all entries, empty all fields and press the submit button again	property	en	search by property_id. To show all entries, empty all fields and press the SUBMIT button again
search by seksjon. to show all entries, empty all fields and press the submit button again	property	en	search by seksjon. To show all entries, empty all fields and press the SUBMIT button again
search for history at this location	property	en	search for history at this location
search for investment entries	property	en	Search for investment entries
search for paid invoices	property	en	Search for paid invoices
search for voucher id	property	en	Search for voucher id
seksjons nr	property	en	Seksjons nr
select	property	en	Select
select a actor type	property	en	Select a actor type
select a agreement type	property	en	Select a agreement type
select a custom function	property	en	Select a custom function
select a datatype	property	en	Select a datatype
select a entity type	property	en	Select a entity type
select a location	property	en	select a location
select a location!	property	en	select a location!
select a rental agreement type	property	en	Select a rental agreement type
select a service agreement type	property	en	Select a service agreement type
select a standard-code from the norwegian standard	property	en	Select a standard-code from the norwegian standard
select a tenant	property	en	Select a tenant
select agreement	property	en	select agreement
select agreement group	property	en	Select agreement group
select agreement_group	property	en	select agreement_group
select all	property	en	Select All
select b-responsible	property	en	Select B-Responsible
select branch	property	en	Select branch
select building part	property	en	Select building part
select category	property	en	Select category
select chapter	property	en	Select chapter
select column	property	en	Select Column
select conversion	property	en	Select conversion
select coordinator	property	en	Select coordinator
select date	property	en	Select date
select date for the file to roll back	property	en	Select date for the file to roll back
select date the document was created	property	en	Select date the document was created
select default vendor category	property	en	Select default vendor category
select either a location or an entity	property	en	select either a location or an entity
select either a location or an equipment	property	en	select either a location or an equipment
select email	property	en	Select email
select file to roll back	property	en	Select file to roll back
select file to upload	property	en	Select file to upload
select grouping	property	en	Select grouping
select invoice type	property	en	Select Invoice Type
select key responsible	property	en	Select key responsible
select location level	property	en	select location level
select nullable	property	en	Select nullable
select owner	property	en	Select owner
select per button !	property	en	Select per button !
select rental type	property	en	Select rental type
select request	property	en	Select request
select responsible	property	en	Select responsible
select status	property	en	Select status
select submodule	property	en	Select submodule
select the account class the selection belongs to	property	en	Select the account class the selection belongs to.
select the agreement group this activity belongs to.	property	en	Select the agreement group this activity belongs to.
select the agreement the pricebook belongs to. to do not use a category select no category	property	en	Select the agreement the pricebook belongs to. To do not use a category select NO CATEGORY
select the agreement this activity belongs to.	property	en	Select the agreement this activity belongs to.
select the agreement_group the pricebook belongs to. to do not use a category select no category	property	en	Select the agreement_group the pricebook belongs to. To do not use a category select NO CATEGORY
select the appropriate condition degree	property	en	Select the appropriate condition degree
select the appropriate consequence by breakdown of this component for this theme	property	en	Select the appropriate consequence by breakdown of this component for this theme
select the appropriate propability for worsening of the condition	property	en	Select the appropriate propability for worsening of the condition
select the appropriate tax code	property	en	select the appropriate tax code
select the branch for this activity.	property	en	Select the branch for this activity.
select the branch for this document	property	en	Select the branch for this document
select this contact	property	en	Select this contact
select the branches for this project	property	en	Select the branches for this project
select the branches for this request	property	en	Select the branches for this request
select the budget responsible	property	en	Select the budget responsible
select the building part for this activity.	property	en	Select the building part for this activity.
select the category the actor belongs to. to do not use a category select no category	property	en	Select the category the actor belongs to. To do not use a category select NO CATEGORY
select the category the agreement belongs to. to do not use a category select no category	property	en	Select the category the agreement belongs to. To do not use a category select NO CATEGORY
select the category the alarm belongs to. to do not use a category select no category	property	en	Select the category the alarm belongs to. To do not use a category select NO CATEGORY
select the category the apartment belongs to. to do not use a category select no category	property	en	Select the category the apartment belongs to. To do not use a category select NO CATEGORY
select the category the building belongs to. to do not use a category select no category	property	en	Select the category the building belongs to. To do not use a category select NO CATEGORY
select the category the claim belongs to. to do not use a category select no category	property	en	Select the category the claim belongs to. To do not use a category select NO CATEGORY
select the category the custom belongs to. to do not use a category select no category	property	en	Select the category the custom belongs to. To do not use a category select NO CATEGORY
select the category the document belongs to. to do not use a category select no category	property	en	Select the category the document belongs to. To do not use a category select NO CATEGORY
select the category the entrance belongs to. to do not use a category select no category	property	en	Select the category the entrance belongs to. To do not use a category select NO CATEGORY
select the category the equipment belongs to. to do not use a category select no category	property	en	Select the category the equipment belongs to. To do not use a category select NO CATEGORY
select the category the investment belongs to. to do not use a category select no category	property	en	Select the category the investment belongs to. To do not use a category select NO CATEGORY
select the category the location belongs to. to do not use a category select no category	property	en	Select the category the location belongs to. To do not use a category select NO CATEGORY
select the category the meter belongs to. to do not use a category select no category	property	en	Select the category the meter belongs to. To do not use a category select NO CATEGORY
select the category the owner belongs to. to do not use a category select no category	property	en	Select the category the owner belongs to. To do not use a category select NO CATEGORY
select the category the permissions belongs to. to do not use a category select no category	property	en	Select the category the permissions belongs to. To do not use a category select NO CATEGORY
select the category the pricebook belongs to. to do not use a category select no category	property	en	Select the category the pricebook belongs to. To do not use a category select NO CATEGORY
select the category the project belongs to. to do not use a category select no category	property	en	Select the category the project belongs to. To do not use a category select NO CATEGORY
select the category the property belongs to. to do not use a category select no category	property	en	Select the category the property belongs to. To do not use a category select NO CATEGORY
select the category the report belongs to. to do not use a category select no category	property	en	Select the category the report belongs to. To do not use a category select NO CATEGORY
select the category the request belongs to. to do not use a category select no category	property	en	Select the category the request belongs to. To do not use a category select NO CATEGORY
select the category the r_agreement belongs to. to do not use a category select no category	property	en	Select the category the r_agreement belongs to. To do not use a category select NO CATEGORY
select the category the s_agreement belongs to. to do not use a category select no category	property	en	Select the category the s_agreement belongs to. To do not use a category select NO CATEGORY
select the category the tenant belongs to. to do not use a category select no category	property	en	Select the category the tenant belongs to. To do not use a category select NO CATEGORY
select the category the ticket belongs to. to do not use a category select no category	property	en	Select the category the ticket belongs to. To do not use a category select NO CATEGORY
select the category the workorder belongs to. to do not use a category select no category	property	en	Select the category the workorder belongs to. To do not use a category select NO CATEGORY
select the category. to do not use a category select no category	property	en	Select the category. To do not use a category select NO CATEGORY
select the chapter (for tender) for this activity.	property	en	Select the chapter (for tender) for this activity.
select the coordinator the document belongs to. to do not use a category select no user	property	en	Select the coordinator the document belongs to. To do not use a category select NO USER
select the coordinator the project belongs to. to do not use a category select no user	property	en	Select the coordinator the project belongs to. To do not use a category select NO USER
select the coordinator the request belongs to. to do not use a category select no user	property	en	Select the coordinator the request belongs to. To do not use a category select NO USER
select the customer by clicking this link	property	en	Select the customer by clicking this link
select the date for the first value	property	en	Select the date for the first value
select the date for the update	property	en	Select the date for the update
select the dim b for this invoice. to do not use dim b -  select no dim b	property	en	Select the Dim B for this invoice. To do not use Dim B -  select NO DIM B
select the dim d for this activity. to do not use dim d -  select no dim d	property	en	Select the Dim D for this activity. To do not use Dim D -  select NO DIM D
select the district the part of town belongs to.	property	en	Select the district the part of town belongs to.
user contact info	property	en	User contact info
select the district the selection belongs to. to do not use a district select no district	property	en	Select the district the selection belongs to. To do not use a district select NO DISTRICT
select the document type the document belongs to.	property	en	Select the document type the document belongs to.
select the equipment type the document belongs to. to do not use a type select no equipment type	property	en	Select the equipment type the document belongs to. To do not use a type select NO equipment type
select the estimated end date for the agreement	property	en	Select the estimated end date for the agreement
select the estimated end date for the project	property	en	Select the estimated end date for the Project
select the estimated end date for the request	property	en	Select the estimated end date for the request
select the estimated termination date	property	en	Select the estimated termination date
select the file to import from	property	en	Select the file to import from
select the filter. to show all entries select show all	property	en	Select the filter. To show all entries select SHOW ALL
select the granting group. to do not use a granting group select no granting group	property	en	Select the granting group. To do not use a granting group select NO GRANTING GROUP
select the grouping for this activity.	property	en	Select the grouping for this activity.
select the janitor responsible for this invoice. to do not use janitor -  select no janitor	property	en	Select the janitor responsible for this invoice. To do not use janitor -  select NO JANITOR
select the key responsible for this project	property	en	Select the key responsible for this project
select the level for this information	property	en	Select the level for this information
select the method for this times service	property	en	Select the method for this times service
select the owner	property	en	Select the owner
select the owner type. to show all entries select show all	property	en	Select the owner type. To show all entries select SHOW ALL
select the part of town the building belongs to. to do not use a part of town -  select no part of town	property	en	Select the part of town the building belongs to. To do not use a part of town -  select NO PART OF TOWN
select the part of town the investment belongs to. to do not use a part of town -  select no part of town	property	en	Select the part of town the investment belongs to. To do not use a part of town -  select NO PART OF TOWN
select the part of town the property belongs to. to do not use a part of town -  select no part of town	property	en	Select the part of town the property belongs to. To do not use a part of town -  select NO PART OF TOWN
select the part of town the selection belongs to. to do not use a part of town select no part of town	property	en	Select the part of town the selection belongs to. To do not use a part of town select NO PART OF TOWN
select the priority the selection belongs to.	property	en	Select the priority the selection belongs to.
select the property by clicking this link	property	en	Select the property by clicking this link
select the status the agreement belongs to. to do not use a category select no status	property	en	Select the status the agreement belongs to. To do not use a category select NO STATUS
select the status the agreement group belongs to. to do not use a category select no status	property	en	Select the status the agreement group belongs to. To do not use a category select NO STATUS
select the status the document belongs to. to do not use a category select no status	property	en	Select the status the document belongs to. To do not use a category select NO STATUS
select the status. to do not use a status select no status	property	en	Select the status. To do not use a status select NO STATUS
select the street name	property	en	Select the street name
select the supervisor responsible for this invoice. to do not use supervisor -  select no supervisor	property	en	Select the supervisor responsible for this invoice. To do not use supervisor -  select NO SUPERVISOR
select the template-chapter	property	en	Select the template-chapter
select the tolerance for this activity.	property	en	Select the tolerance for this activity.
select the type  invoice. to do not use type -  select no type	property	en	Select the type  invoice. To do not use type -  select NO TYPE
select the type of conversion:	property	en	Select the type of conversion:
select the type of value	property	en	Select the type of value
select the unit for this activity.	property	en	Select the unit for this activity.
select the user the alarm belongs to.	property	en	Select the user the alarm belongs to.
select the user the document belongs to. to do not use a category select no user	property	en	Select the user the document belongs to. To do not use a category select NO USER
select the user the project belongs to. to do not use a category select no user	property	en	Select the user the project belongs to. To do not use a category select NO USER
select the user the request belongs to. to do not use a category select no user	property	en	Select the user the request belongs to. To do not use a category select NO USER
select the user the selection belongs to. to do not use a user select no user	property	en	Select the user the selection belongs to. To do not use a user select NO USER
select the user the template belongs to. to do not use a category select no user	property	en	Select the user the template belongs to. To do not use a category select NO USER
select the user the workorder belongs to. to do not use a category select no user	property	en	Select the user the workorder belongs to. To do not use a category select NO USER
select the user to edit email	property	en	Select the user to edit email
select the user. to do not use a category select no user	property	en	Select the user. To do not use a category select NO USER
select the users supervisor	property	en	Select the users supervisor
select the vendor by clicking the button	property	en	Select the vendor by clicking the button
select the vendor by clicking this button	property	en	Select the vendor by clicking this button
select the vendor by clicking this link	property	en	Select the vendor by clicking this link
select the vendor the agreement belongs to.	property	en	Select the vendor the agreement belongs to.
select the vendor the r_agreement belongs to.	property	en	Select the vendor the r_agreement belongs to.
select the vendor the s_agreement belongs to.	property	en	Select the vendor the s_agreement belongs to.
select the workorder hour category	property	en	Select the workorder hour category
select this budget account	property	en	Select this budget account
select this dates	property	en	Select this dates
select this ns3420 - code	property	en	Select this ns3420 - code
select this street	property	en	Select this street
select this template to view the details	property	en	Select this template to view the details
select this tenant	property	en	Select this tenant
select this vendor	property	en	Select this vendor
select tolerance	property	en	Select tolerance
select unit	property	en	Select Unit
select user	property	en	Select user
select where to deliver the key	property	en	Select where to deliver the key
select where to fetch the key	property	en	Select where to fetch the key
select year	property	en	select year
send e-mail	property	en	Send e-mail
send order	property	en	Send Order
send the following sms-message to %1 to update status for this order:	property	en	Send the following SMS-message to %1 to update status for this order:
send this order by email	property	en	Send this order by email
sendt by email to	property	en	Sendt by email to
serious	property	en	Serious
serious consequences	property	en	Serious Consequences
service	property	en	Service
service agreement	property	en	service agreement
set grants	property	en	set grants
set permission	property	en	set permission
set tax	property	en	Set tax
set the status of the ticket	property	en	Set the status of the ticket
shared use	property	en	Shared use
shift down	property	en	shift down
shift up	property	en	shift up
show all	property	en	Show all
show calculated cost	property	en	Show calculated cost
show calculated cost on the printview	property	en	Show calculated cost on the printview
show details	property	en	Show details
show in list	property	en	show in list
show in lookup forms	property	en	show in lookup forms
small	property	en	Small
sort the tickets by their id	property	en	Sort the tickets by their ID
sort the tickets by their priority	property	en	Sort the tickets by their priority
sorting	property	en	sorting
space	property	en	space
sql	property	en	sql
standard	property	en	Standard
standard description	property	en	standard description
standard has been edited	property	en	standard has been edited
standard has been saved	property	en	standard has been saved
standard has not been edited	property	en	Standard has NOT been edited
standard id	property	en	standard id
standard prefix	property	en	Standard prefix
start	property	en	start
start date	property	en	Start date
start project	property	en	Start project
start report	property	en	Start Report
start this entity	property	en	start this entity
start this entity from	property	en	Start this entity from
started	property	en	Started
status	property	en	Status
status changed	property	en	Status changed
status for the entity category	property	en	Status for the entity category
status has been added	property	en	status has been added
status has been edited	property	en	Status has been edited
status has not been saved	property	en	Status has NOT been saved
status code	property	en	Status code
status id	property	en	status ID
statustext	property	en	Statustext
statustext not entered!	property	en	Statustext not entered!
street	property	en	Street
street name	property	en	Street name
street number	property	en	Street number
subject	property	en	Subject
subject changed	property	en	Subject changed
subject has been updated	property	en	Subject has been updated
submit	property	en	submit
submit the search string	property	en	Submit the search string
sum	property	en	Sum
sum calculation	property	en	Sum calculation
sum deviation	property	en	Sum deviation
sum of calculation	property	en	Sum of calculation
sum tax	property	en	Sum tax
sum workorder	property	en	Sum workorder
summary	property	en	Summary
supervisor	property	en	Supervisor
table %1 has been saved	property	en	table %1 has been saved
table could not be added	property	en	table could not be added
table has not been saved	property	en	Table has NOT been saved
table name	property	en	Table Name
tax code	property	en	Tax code
template	property	en	template
template %1 is added	property	en	template %1 is added
template id	property	en	Template ID
tenant	property	en	Tenant
tenant %1 has been edited	property	en	tenant %1 has been edited
tenant %1 has been saved	property	en	tenant %1 has been saved
tenant claim	property	en	Tenant claim
tenant id	property	en	tenant id
tenant phone	property	en	Tenant phone
termination date	property	en	termination date
test cron	property	en	test cron
text	property	en	Text
that vendor id is not valid !	property	en	That Vendor ID is not valid !
the address to which this order will be sendt	property	en	The address to which this order will be sendt
the apartment is private. if the apartment should be public, uncheck this box	property	en	The apartment is private. If the apartment should be public, uncheck this box
the apartment is public. if the apartment should be private, check this box	property	en	The apartment is public. If the apartment should be private, check this box
users email is updated	property	en	Users email is updated
the building is private. if the building should be public, uncheck this box	property	en	The building is private. If the building should be public, uncheck this box
the building is public. if the building should be private, check this box	property	en	The building is public. If the building should be private, check this box
the entrance is private. if the entrance should be public, uncheck this box	property	en	The entrance is private. If the entrance should be public, uncheck this box
the entrance is public. if the entrance should be private, check this box	property	en	The entrance is public. If the entrance should be private, check this box
the file is already imported !	property	en	The file is already imported !
the file is empty or removed!	property	en	The file is empty or removed!
the location is private. if the location should be public, uncheck this box	property	en	The location is private. If the location should be public, uncheck this box
the location is public. if the location should be private, check this box	property	en	The location is public. If the location should be private, check this box
the mail server returned	property	en	The mail server returned
the number of %1 hour is added!	property	en	the number of %1 hour is added!
the project has not been saved	property	en	the project has not been saved
the property is private. if the property should be public, uncheck this box	property	en	The property is private. If the property should be public, uncheck this box
the property is public. if the property should be private, check this box	property	en	The property is public. If the property should be private, check this box
the recipient did not get the email:	property	en	The recipient did not get the email:
the total amount to claim	property	en	The total amount to claim
the workorder has not been saved	property	en	the workorder has not been saved
this account is not valid:	property	en	This account is not valid:
this activity code is already registered!	property	en	This activity code is already registered!
this agreement code is already registered!	property	en	This agreement code is already registered!
this agreement group code is already registered!	property	en	This agreement group code is already registered!
this apartment id does not exist!	property	en	This apartment ID does not exist!
this apartment is already registered!	property	en	This apartment is already registered!
this attribute turn up as disabled in the form	property	en	This attribute turn up as disabled in the form
this building id does not exist!	property	en	This Building ID does not exist!
this building is already registered!	property	en	This building is already registered!
this dim a is not valid:	property	en	This Dim A is not valid:
this dim d is not valid:	property	en	This Dim D is not valid:
this entrance id does not exist!	property	en	This Entrance ID does not exist!
this entrance is already registered!	property	en	This entrance is already registered!
this equipment id already exists!	property	en	This Equipment ID already exists!
this equipment id does not exist!	property	en	This equipment ID does not exist!
this file already exists !	property	en	This file already exists !
this location id does not exist!	property	en	This location ID does not exist!
this location is already registered!	property	en	This location is already registered!
this location parent id does not exist!	property	en	This location parent ID does not exist!
this meter id is already registered!	property	en	This meter id is already registered!
this property id does not exist!	property	en	This property ID does not exist!
this report id already exists!	property	en	This report ID already exists!
this report id does not exist!	property	en	This report ID does not exist!
this user has not defined an email address !	property	en	This user has not defined an email address !
this vendor is already registered for this activity	property	en	This Vendor is already registered for this activity
ticket	property	en	Ticket
ticket has been saved	property	en	Ticket has been saved
ticket has been updated	property	en	Ticket has been updated
ticket id	property	en	Ticket ID
time	property	en	Time
time created	property	en	time created
times	property	en	Times
timing	property	en	timing
title	property	en	Title
to	property	en	To
to alter the priority key	property	en	To alter the priority key
to date	property	en	to date
tolerance	property	en	tolerance
total	property	en	Total
total cost	property	en	Total Cost
total records	property	en	Total records
total sum	property	en	Total sum
tracking	property	en	tracking
transfer	property	en	Transfer
true	property	en	True
type	property	en	type
type invoice ii	property	en	Type invoice II
type of changes	property	en	Type of changes
uncheck to debug the result	property	en	Uncheck to debug the result
unit	property	en	Unit
up	property	en	up
update	property	en	Update
update a single entry by passing the fields.	property	en	Update a single entry by passing the fields.
update email	property	en	Update email
update file	property	en	Update file
update project	property	en	Update project
update selected investments	property	en	update selected investments
update subject	property	en	update subject
update the category to not active based on if there is only nonactive apartments	property	en	Update the category to not active based on if there is only nonactive apartments
update the not active category for locations	property	en	Update the not active category for locations
upload file	property	en	Upload file
user	property	en	User
user gratification	property	en	user gratification
username / group	property	en	Username / Group
users	property	en	Users
users phone is updated	property	en	Users phone is updated
value	property	en	Value
values	property	en	values
varchar	property	en	varchar
vendor	property	en	Vendor
vendor has been added	property	en	Vendor has been added
vendor id	property	en	Vendor ID
vendor name	property	en	Vendor Name
version	property	en	Version
view	property	en	view
view apartment	property	en	view apartment
view building	property	en	view building
view document	property	en	view document
view documents for this location/entity	property	en	view documents for this location/entity
view documents for this location/equipment	property	en	view documents for this location/equipment
view edit the prize for this activity	property	en	view edit the prize for this activity
view entrance	property	en	view entrance
view equipment	property	en	view equipment
view gab	property	en	View gab
view gab detail	property	en	view gab detail
view gab-info	property	en	View gab-info
view information about the document	property	en	view information about the document
view information about the gab	property	en	view information about the gab
view investment	property	en	view investment
view location	property	en	view location
view map	property	en	View map
view meter	property	en	View meter
view or edit prizing history of this element	property	en	view or edit prizing history of this element
view project	property	en	View Project
view property	property	en	view property
view report	property	en	view report
view request	property	en	View request
view template detail	property	en	view template detail
view tender	property	en	View tender
view the apartment	property	en	view the apartment
view the attrib	property	en	view the attrib
view the budget account	property	en	view the budget account
view the building	property	en	view the building
view the category	property	en	view the category
view the claim	property	en	view the claim
view the complete workorder	property	en	View the complete workorder
view the complete workorder as a tender for bidding	property	en	View the complete workorder as a tender for bidding
view the document	property	en	view the document
view the entity	property	en	view the entity
view the entrance	property	en	view the entrance
view the equipment	property	en	view the equipment
view the gab	property	en	view the gab
view the location	property	en	view the location
view the meter	property	en	view the meter
view the method	property	en	view the method
view the owner	property	en	view the owner
view the part of town	property	en	view the part of town
view the project	property	en	view the project
view the property	property	en	view the property
view the report	property	en	view the report
view the request	property	en	view the request
view the standard	property	en	view the standard
view the template	property	en	view the template
view the tenant	property	en	view the tenant
view the ticket	property	en	view the ticket
view the vendor(s) for this activity	property	en	view the vendor(s) for this activity
view the workorder	property	en	view the workorder
view ticket detail	property	en	view ticket detail
view workorder	property	en	View Workorder
view/edit the history	property	en	View/Edit the history
voucher	property	en	voucher
voucher date	property	en	Voucher Date
voucher id	property	en	Voucher ID
voucher is updated	property	en	Voucher is updated
voucher is updated:	property	en	voucher is updated:
voucher period is updated	property	en	voucher period is updated
weight for prioritising	property	en	Weight for prioritising
what is the current status of this document ?	property	en	What is the current status of this document ?
what is the current status of this equipment ?	property	en	What is the current status of this equipment ?
what is the current status of this project ?	property	en	What is the current status of this project ?
what is the current status of this report ?	property	en	What is the current status of this report ?
what is the current status of this request ?	property	en	What is the current status of this request ?
what is the current status of this workorder ?	property	en	What is the current status of this workorder ?
where to deliver the key	property	en	Where to deliver the key
where to fetch the key	property	en	Where to fetch the key
where to pick up the key	property	en	Where to pick up the key
which entity type is to show up in location forms	property	en	Which entity type is to show up in location forms
work:____________	property	en	work:____________
workorder	property	en	Workorder
workorder %1 has been edited	property	en	workorder %1 has been edited
workorder %1 has been saved	property	en	workorder %1 has been saved
workorder end date	property	en	Workorder end date
workorder entry date	property	en	Workorder entry date
workorder id	property	en	Workorder ID
workorder %1 is sent by email to %2	property	en	Workorder %1 is sent by email to %2
workorder start date	property	en	Workorder start date
workorder status	property	en	Workorder status
workorder template	property	en	Workorder template
workorder title	property	en	Workorder title
workorder user	property	en	Workorder User
write off	property	en	Write off
property	controller	no	Eiendom
write off period	property	en	Write off period
w_cost	property	en	w_cost
year	property	en	Year
yes	property	en	yes
you have entered an invalid end date !	property	en	You have entered an invalid end date !
you have entered an invalid start date !	property	en	You have entered an invalid start date !
you have no edit right for this project	property	en	You have no edit right for this project
you have to select a budget responsible for this invoice in order to add the invoice	property	en	You have to select a budget responsible for this invoice in order to add the invoice
you have to select a budget responsible for this invoice in order to make the import	property	en	You have to select a budget responsible for this invoice in order to make the import
you have to select the conversion for this import	property	en	You have to select the Conversion for this import
you have to select type of invoice	property	en	You have to select type of invoice
your message could not be sent by mail!	property	en	Your message could not be sent by mail!
your message could not be sent!	property	en	Your message could not be sent!
.project	property	en	Project
.project.workorder	property	en	Workorder
.project.request	property	en	Request
.tenant_claim	property	en	Tenant claim
.ticket	property	en	Ticket
address book	common	no	Addressebok
addressbook	common	no	Addressebook
birthday	addressbook	no	Fødselsdag
city	addressbook	no	By
e-mail	addressbook	no	E-Post
fax	addressbook	no	Telefaks
first name	addressbook	no	Fornavn
home phone	addressbook	no	Hjemme telefon
last name	addressbook	no	Etternavn
mobile	addressbook	no	Mobil
notes	addressbook	no	Annet
other number	addressbook	no	Annet nummer
pager	addressbook	no	Personsøker
state	addressbook	no	Stat
street	addressbook	no	Gate
today is %1's birthday!	common	no	I dag har %1 fødselsdag!
tomorrow is %1's birthday.	common	no	I morgen er det %1's fødselsdag.
work phone	addressbook	no	Arbeids telefon
zip code	addressbook	no	Postnummer
communication types manager	common	no	Kommunikasjonstyper
communication descriptions manager	common	no	Kommunikasjonstypebeskrivelse
location manager	common	no	Lokasjonstyper
notes types manager	common	no	Notattyper
custom fields on org-person	common	no	Egentilpassede felt for org-person
%1 - %2 of %3	addressbook	en	%1 - %2 of %3
%1 - %2 of %3 %4	addressbook	en	%1 - %2 of %3 %4
%1 - %2 of %3 user accounts	addressbook	en	%1 - %2 of %3 user accounts
%1 records imported	addressbook	en	%1 records imported
%1 records read (not yet imported, you may go %2back%3 and uncheck test import)	addressbook	en	%1 records read (not yet imported, you may go %2back%3 and uncheck Test Import)
%1 was found %2 times in %3	addressbook	en	%1 was found %2 times in %3
<b>no conversion type &lt;none&gt; could be located.</b>  please choose a conversion type from the list	addressbook	en	<b>No conversion type &lt;none&gt; could be located.</b>  Please choose a conversion type from the list
@-eval() is only availible to admins!!!	addressbook	en	@-eval() is only availible to admins!!!
add a single entry by passing the fields.	addressbook	en	Add a single entry by passing the fields.
add custom field	addressbook	en	Add Custom Field
add new	addressbook	en	Add new
address	addressbook	en	Address
address book - vcard in	addressbook	en	Address book - VCard in
address book - view	addressbook	en	Address book - view
address data for	addressbook	en	Address Data for
address line 2	addressbook	en	Address Line 2
address line 3	addressbook	en	Address Line 3
address type	addressbook	en	Address Type
addressbook preferences	addressbook	en	Addressbook preferences
addressbook-fieldname	addressbook	en	Addressbook-Fieldname
addvcard	addressbook	en	Add VCard
all	addressbook	en	All
alt. csv import	addressbook	en	Alt. CSV Import
applications	addressbook	en	applications
apply for	addressbook	en	Apply for
are you sure you want to delete this field?	addressbook	en	Are you sure you want to delete this field?
bbs phone	addressbook	en	BBS Phone
birthday	common	en	Birthday
birthdays	common	en	Birthdays
blank	addressbook	en	Blank
both	addressbook	en	Both
business	common	en	Business
business address type	addressbook	en	Business Address Type
business city	addressbook	en	Business City
business country	addressbook	en	Business Country
business email	addressbook	en	Business EMail
business email type	addressbook	en	Business EMail Type
business fax	addressbook	en	Business Fax
business phone	addressbook	en	Business Phone
business state	addressbook	en	Business State
business street	addressbook	en	Business Street
business zip code	addressbook	en	Business Postal Code
car phone	addressbook	en	Car Phone
categorize	addressbook	en	Categorize
cell phone	addressbook	en	cell phone
city	common	en	City
communication data for	addressbook	en	Communication Data for
communications	addressbook	en	Communications
company	common	en	Company
company name	common	en	Company Name
contacts	common	en	Contacts
no district	controller	no	Distrikt ikke valgt
copied by %1, from record #%2.	addressbook	en	Copied by %1, from record #%2.
country	common	en	Country
csv-fieldname	addressbook	en	CSV-Fieldname
csv-filename	addressbook	en	CSV-Filename
current	addressbook	en	Current
custom	addressbook	en	Custom
custom fields	addressbook	en	Custom Fields
debug output in browser	addressbook	en	Debug output in browser
defaul	addressbook	en	Defaul
default	addressbook	en	Default
default filter	addressbook	en	Default Filter
default preferences	addressbook	en	Default preferences
delete a single entry by passing the id.	addressbook	en	Delete a single entry by passing the id.
department	common	en	Department
domestic	addressbook	en	Domestic
download	addressbook	en	Download
edit custom field	addressbook	en	Edit Custom Field
empty	addressbook	en	Empty
export contacts	addressbook	en	Export Contacts
export file name	addressbook	en	Export file name
export from addressbook	addressbook	en	Export from Addressbook
extra	addressbook	en	Extra
fax	addressbook	en	Fax
field %1 has been added !	addressbook	en	Field %1 has been added !
field %1 has been updated !	addressbook	en	Field %1 has been updated !
field name	addressbook	en	Field Name
fields to show in address list	addressbook	en	Fields to show in address list
fieldseparator	addressbook	en	Fieldseparator
filter by:	addressbook	en	Filter by:
forced preferences	addressbook	en	Forced preferences
full name	addressbook	en	Full Name
general data	addressbook	en	General Data
geo	addressbook	en	GEO
go back	addressbook	en	Go back
grant addressbook access	common	en	Grant Addressbook Access
home address type	addressbook	en	Home Address Type
home city	addressbook	en	Home City
home country	addressbook	en	Home Country
home email	addressbook	en	Home EMail
home email type	addressbook	en	Home EMail Type
home phone	addressbook	en	Home Phone
home state	addressbook	en	Home State
home street	addressbook	en	Home Street
home zip code	addressbook	en	Home ZIP Code
import	addressbook	en	Import
import contacts	addressbook	en	Import Contacts
import csv-file into addressbook	addressbook	en	Import CSV-File into Addressbook
import file	addressbook	en	Import File
import from ldif, csv, or vcard	addressbook	en	Import from LDIF, CSV, or VCard
import from outlook	addressbook	en	Import from Outlook
international	addressbook	en	International
isdn phone	addressbook	en	ISDN Phone
label	addressbook	en	Label
ldif	addressbook	en	LDIF
line 2	addressbook	en	Line 2
message phone	addressbook	en	Message Phone
middle name	addressbook	en	Middle Name
mobile	addressbook	en	Mobile
mobile phone	addressbook	en	Mobile Phone
modem phone	addressbook	en	Modem Phone
more data	addressbook	en	More data
no vcard	addressbook	en	No VCard
number of records to read (<=200)	addressbook	en	Number of records to read (<=200)
org data	addressbook	en	Org Data
organizations	addressbook	en	Organizations
organizations data	addressbook	en	Organizations Data
orgs	addressbook	en	Orgs
other data for	addressbook	en	Other Data for
other number	addressbook	en	Other Number
other phone	addressbook	en	Other Phone
others	addressbook	en	Others
pager	common	en	Pager
parcel	addressbook	en	Parcel
person data	addressbook	en	Person Data
person extra fields for	addressbook	en	Person extra fields for
persons	addressbook	en	Persons
phone	addressbook	en	Phone
phone numbers	common	en	Phone Numbers
please enter a name for that field !	addressbook	en	Please enter a name for that field !
pref	addressbook	en	pref
preferred	addressbook	en	Preferred
prefix	addressbook	en	Prefix
public key	addressbook	en	Public Key
read a list of entries.	addressbook	en	Read a list of entries.
read a single entry by passing the id and fieldlist.	addressbook	en	Read a single entry by passing the id and fieldlist.
reason	addressbook	en	reason
record access	addressbook	en	Record Access
record owner	addressbook	en	Record owner
remove	addressbook	en	remove
search:	addressbook	en	Search:
select fields	addressbook	en	select fields
show birthday reminders on main screen	addressbook	en	Show birthday reminders on main screen
startrecord	addressbook	en	Startrecord
state	common	en	State
street	common	en	Street
suffix	addressbook	en	Suffix
short_month 11 capitalized	controller	no	Nov
short_month 12 capitalized	controller	no	Des
test import (show importable records <u>only</u> in browser)	addressbook	en	Test Import (show importable records <u>only</u> in browser)
that field name has been used already !	addressbook	en	That field name has been used already !
this person's first name was not in the address book.	addressbook	en	This person's first name was not in the address book.
this person's last name was not in the address book.	addressbook	en	This person's last name was not in the address book.
today is %1's birthday!	common	en	Today is %1's birthday!
tomorrow is %1's birthday.	common	en	Tomorrow is %1's birthday.
type	addressbook	en	Type
update a single entry by passing the fields.	addressbook	en	Update a single entry by passing the fields.
use country list	addressbook	en	Use Country List
value	addressbook	en	Value
vcard	common	en	VCard
vcards require a first name entry.	addressbook	en	VCards require a first name entry.
vcards require a last name entry.	addressbook	en	Vcards require a last name entry.
video phone	addressbook	en	Video Phone
voice phone	addressbook	en	Voice Phone
work phone	addressbook	en	Work Phone
you must select a vcard. (*.vcf)	addressbook	en	You must select a vcard. (*.vcf)
you must select at least 1 column to display	addressbook	en	You must select at least 1 column to display
your preferences	addressbook	en	Your preferences
zip code	common	en	ZIP Code
missing value for required	controller	no	Mangler registrering for obligatorisk
please enter billable hours	controller	no	Angi egne timer
my assigned controls	controller	no	Mine kontroller
add case	controller	no	Registrer sak
cases	common	no	Registrerte saker
add ticket	controller	no	Registrer melding
add_check_list_to_location	controller	no	Registrer kontroll for bygg
added	controller	no	Lagt til
deleted	controller	no	Slettet
bookmark	controller	no	Bokmerke
location_connections	controller	no	Byggknytning
control	controller	no	Kontroll
controller	common	no	Kontroll
register control item	controller	no	Legg til nytt kontrollpunkt
edit control item	controller	no	Endre kontrollpunkt
entity	controller	no	Komponentregister
view control item	controller	no	Vis kontrollpunkt
control_item_type_1	controller	no	Ved innskriving av kommentar
control_item_type_2	controller	no	Ved innskriving av måling i et tekstfelt
control_item_type_3	controller	no	Ved valg av verdi fra nedtrekksliste
control_item_type_4	controller	no	Ved valg av verdi fra radioknapper
control_item_type_5	controller	no	Ved valg av verdi fra Avkrysning
control_helptext	controller	no	Her kommer hjelpetekst for å opprette en kontroll
check_list	controller	no	Sjekkliste
chosen attributes	controller	no	Valgte datafelter
calendar_overview	controller	no	Kontrollplan
check_lists	controller	no	Sjekklister
choose_control_groups	controller	no	Velg kontrollgrupper
choose_control_items	controller	no	Velg kontrollpunkt
choose_building_type	controller	no	Velg byggtype
choose_building_category	controller	no	Velg byggkategori
choose_district	controller	no	Velg distrikt
district	controller	no	Distrikt
user	controller	no	Bruker
choose_part_of_town	controller	no	Velg bydel
datatable_msg_empty	controller	no	Ingen data
datatable_msg_error	controller	no	Datafeil
datatable_msg_loading	controller	no	Laster data
sort_check_list	controller	no	Sorter sjekkliste
show_check_lists	controller	no	Vis sjekklister
save_check_list	controller	no	Lagre sjekkliste
title	controller	no	kontroll
close	controller	no	Lukk
clear	controller	no	Nullstill
month 1	controller	no	januar
month 2	controller	no	februar
month 3	controller	no	mars
month 4	controller	no	april
month 5	controller	no	mai
month 6	controller	no	juni
month 7	controller	no	juli
month 8	controller	no	august
month 9	controller	no	september
month 10	controller	no	oktober
month 11	controller	no	november
month 12	controller	no	desember
month 0 capitalized	controller	no	Ikke tilgjengelig
month 1 capitalized	controller	no	Januar
month 2 capitalized	controller	no	Februar
month 3 capitalized	controller	no	Mars
month 4 capitalized	controller	no	April
month 5 capitalized	controller	no	Mai
month 6 capitalized	controller	no	Juni
month 7 capitalized	controller	no	Juli
month 8 capitalized	controller	no	August
month 9 capitalized	controller	no	September
month 10 capitalized	controller	no	Oktober
month 11 capitalized	controller	no	November
month 12 capitalized	controller	no	Desember
short_month 1 capitalized	controller	no	Jan
short_month 2 capitalized	controller	no	Feb
short_month 3 capitalized	controller	no	Mar
short_month 4 capitalized	controller	no	Apr
short_month 5 capitalized	controller	no	Mai
short_month 6 capitalized	controller	no	Jun
short_month 7 capitalized	controller	no	Jul
short_month 8 capitalized	controller	no	Aug
short_month 9 capitalized	controller	no	Sep
short_month 10 capitalized	controller	no	Okt
monthly	controller	no	Månedlig
month	controller	no	Måned
no part of town	controller	no	Bydel ikke valgt
calendar_months	controller	no	["Januar","Februar","Mars","April","Mai","Juni","Juli","August","September","Oktober","November","Desember"]
calendar_weekdays	controller	no	["Sø","Ma","Ti","On","To","Fr","Lø"]
select_date	controller	no	Velg dato
select	controller	no	Velg
location_code	controller	no	Enhetsnummer
included_units	controller	no	Kontrollens enheter
floor	controller	no	Etasje
section	controller	no	Seksjon
room	controller	no	Rom
save	controller	no	Lagre
save_order	controller	no	Lagre rekkefølge
1-6 characters	controller	no	1-6 tegn
6 characters	controller	no	6 tegn
active	controller	no	Aktiv
add	controller	no	Legg til
add_location	controller	no	Registrer kontroll til bygg
all	controller	no	Alle
all_locations	controller	no	Enheter fra eiendomsregisteret
annually	controller	no	Årlig
back	controller	no	Tilbake
building	controller	no	Bygg
cancel	controller	no	Avbryt
comment	controller	no	Kommentar
comments	controller	no	Kommentarer
count	controller	no	Antall
count_suffix	controller	no	stk
create_shortcut	controller	no	Ny snarvei
date_end	controller	no	Gjelder til
date	controller	no	Dato
date_start	controller	no	Gjelder fra
delete	controller	no	Slett
description	controller	no	Beskrivelse
details	controller	no	Detaljer
do_not_exist	controller	no	Eksisterer ikke
edit	controller	no	Redigér
elements_pr_page	controller	no	elementer per side
elements	controller	no	Elementer
every_second_week	controller	no	Hver 14. dag
export	controller	no	Eksport
export_to	controller	no	Eksporter
filters	controller	no	Filtre
finish	controller	no	Avslutt
fire_drawings	controller	no	Branntegninger
first	controller	no	Første
first_half	controller	no	1. halvår
first_quarter	controller	no	1. kvartal
fixed	controller	no	Tidsbestemt
fourth_quarter	controller	no	4. kvartal
from	controller	no	Fra
f_select_columns	controller	no	Velg kolonner
gab	controller	no	GAB
gab_id	controller	no	GAB
half-year	controller	no	Halvårig
hidden	controller	no	Skjult
hidden_for_pick	controller	no	skjult
house_number	controller	no	Husnummer
id	controller	no	ID
identifier	controller	no	Identifikator
inactive	controller	no	Inaktiv
interval	controller	no	Intervall
is_active	controller	no	Aktiv
is_inactive	controller	no	Inaktiv
is_executed	controller	no	Utført
land_title	controller	no	Gnr/Bnr
lastname	controller	no	Etternavn
last	controller	no	Siste
last_updated	controller	no	Sist oppdatert
level	controller	no	Nivå
link	controller	no	Lenke
locations_for_control	controller	no	Bygg tilknyttet kontroll
make_pdf	controller	no	Lag PDF for utskrift
message	controller	no	Melding
messages_form_error	controller	no	Skjemaet inneholder en feil.
messages_general	controller	no	Feil i feltet
messages_isint	controller	no	Feltet må inneholde et heltall
messages_isnumeric	controller	no	Feltet må inneholde et tall
messages_not_valid_date	controller	no	Må være en gyldig dato
messages_number_out_of_range	controller	no	Tallet er over eller under tillatte verdier
messages_required_field	controller	no	Dette feltet er påkrevd
messages_right_click_to_add	controller	no	Høyreklikk for å legge til
messages_saved_form	controller	no	Informasjonen ble lagret.
messages_string_too_long	controller	no	Teksten er for lang
missing responsibility id.	controller	no	Ansvar mangler.
mobile_phone	controller	no	Mobiltelefon
name	controller	no	Navn
never	controller	no	Aldri
new_notification	controller	no	Nytt varsel
next	controller	no	Neste
no	controller	no	Nei
none	controller	no	Ingen
nobody	controller	no	Ingen
no_hits	controller	no	Ingen treff
no_value	controller	no	Ingen
not_available	controller	no	Ikke tilgjengelig
notification_status	controller	no	Varsel
notifications	controller	no	Varsler
notification_optgroup_groups	controller	no	Grupper
notification_optgroup_users	controller	no	Brukere
not_started	controller	no	Ikke startet
not_available_nor_hidden	controller	no	tilgjengelig eller skjult
object_number	controller	no	Objektnummer
objno_name_address	controller	no	Objektnummer/navn/adresse
occupied	controller	no	Opptatt
of_total	controller	no	av totalt
ok	controller	no	Ok
ods	controller	no	ODS
only_one_time	controller	no	Status
or	controller	no	eller
others	controller	no	Annet
out_of_operation	controller	no	Ikke i drift
panels	controller	no	Vinduer
phone	controller	no	Telefon
postal_code_place	controller	no	Postnummer/-sted
previous	controller	no	Forrige
propertyident	controller	no	G.nr. / B.nr. / F.nr. / S.nr.
property_id	controller	no	BKB identifikator
quarterly	controller	no	Kvartalvis
recurrence	controller	no	Gjentakelse
registered	controller	no	Registrert
remove	controller	no	Fjern
controller	controller	no	Kontroll
reports	controller	no	Rapporter
receipt	controller	no	Kvittering
reset	controller	no	Nullstill
responsibility	controller	no	Ansvar
responsibility_id	controller	no	Ansvar
responsibility id must be 6 characters.	controller	no	Ansvar må være seks tegn.
responsibility_id_not_numeric	controller	no	Ansvar må være et tall
running	controller	no	Løpende
save_control_item	controller	no	Lagre kontrollpunkt
save_check_item	controller	no	Lagre sjekkpunkt
search_for	controller	no	Søk etter
search_options	controller	no	Søkevalg
search	controller	no	Søk
search_where	controller	no	i
second_half	controller	no	2. halvår
second_quarter	controller	no	2. kvartal
security	controller	no	Sikkerhet
select_all_options	controller	no	&lt;Alle$gt;
select_date_valid_year	controller	no	Vennligst velg et gyldig år
service	controller	no	Tjeneste
service_id	controller	no	Tjeneste
service id must be 5 characters.	controller	no	Tjeneste må være fem tegn.
service_id_not_numeric	controller	no	Tjeneste må være et tall
service_exist	controller	no	Eksisterer
shortcuts	controller	no	Snarveier
show	controller	no	Vis
shows_from	controller	no	Viser fra
started	controller	no	Startet
status_before	controller	no	før
status_date	controller	no	den
status_unknown	controller	no	Ukjent status
status	controller	no	Status
status done	controller	no	Utført
status not done	controller	no	Ikke utført
done with open deviation	controller	no	Utført med åpne avvik
success	controller	no	Suksess
sum	controller	no	Sum
system setting for responsibility id for the current user must be 6 characters.	controller	no	Systeminnstilling for ansvar må være seks tegn.
target_me	controller	no	Meg selv
target_none	controller	no	Ingen
third_quarter	controller	no	3. kvartal
t_functions	controller	no	Funksjoner
to	controller	no	til
to_the_top	controller	no	Til toppen
type	controller	no	Type
units	controller	no	Enheter
unit_id	controller	no	Enhetsid
unit_name	controller	no	Enhetsnavn
update	controller	no	Oppdatér
upload	controller	no	Last opp
url	controller	no	Nettsted
username	controller	no	Brukernavn
user_or_group	controller	no	Bruker/Gruppe
unable_to_connect_to_database	controller	no	Problemer med å koble til databasen.
unknown_user	controller	no	Personen finnes ikke.
weekly	controller	no	Ukentlig
working_on	controller	no	Kontrakter under arbeid
year	controller	no	År
yes	controller	no	Ja
control_items	controller	no	Kontrollpunkt
control_item	controller	no	Kontrollpunkt
procedure	controller	no	Prosedyre
procedures	controller	no	Prosedyrer
procedure title	controller	no	Navn
t_new_procedure	controller	no	Ny prosedyre
f_new_procedure	controller	no	Legg til
procedure purpose	controller	no	Formål
procedure responsibility	controller	no	Ansvar og myndighet
procedure description	controller	no	Beskrivelse
procedure reference	controller	no	Referanse
procedure attachment	controller	no	Vedlegg
procedure start date	controller	no	Startdato
procedure end date	controller	no	Sluttdato
procedure valid from date	controller	no	Gyldig fra
new control item	controller	no	Nytt kontrollpunkt
control item title	controller	no	Kontrollpunkt
control_group	controller	no	Kontrollgruppe
control group	controller	no	Kontrollgruppe
control_groups	controller	no	Kontrollgrupper
control_area	controller	no	Kontrollområde
control area	controller	no	Kontrollområde
control_areas	controller	no	Kontrollområder
use acl for control areas	common	no	Bruk rettighetsstyring på kontrollområder
control item what to do	controller	no	Hva skal gjøres
not selected	controller	no	Ingen valgt
searchfield	controller	no	Søkefelt
new	controller	no	Ny
pending	controller	no	Venter
accepted	controller	no	Akseptert
rejected	controller	no	Avvist
new control group	controller	no	Ny kontrollgruppe
control group title	controller	no	Tittel
building part	controller	no	Bygningsdel
locations	controller	no	Lokasjoner
component	controller	no	Utstyr
control_locations	controller	no	Lokasjoner
control_component	controller	no	Utstyr
new control	controller	no	Ny kontroll
revisit	controller	no	Revidér
procedure revision	controller	no	Versjon
procedure revision date	controller	no	Revisjonsdato
font style	controller	no	Font stil
lists	controller	no	Lister
insert item	controller	no	Sett inn
control title	controller	no	Tittel
start_date	controller	no	Startdato
planned_date	controller	no	Planlagtdato
end_date	controller	no	Sluttdato
view_locations_for_control	controller	no	Vis kontroller for lokasjon
add_locations_for_control	controller	no	Legg til knytning mot lokasjon
view_component_for_control	controller	no	Vis knytning mot utstyr
add_component_for_control	controller	no	Legg til knytning mot utstyr
component_for_control	controller	no	Utstyr tilknyttet kontroll
choose_component_type	controller	no	Velg utstyrstype
choose_component_category	controller	no	Velg utstyrskategori
component_category_internal	controller	no	Internt utstyrsregister
component_category_ifc	controller	no	IFC
invert_checkboxes	controller	no	Inverter merking
controlid	controller	no	Kontroll-id
bim_id	controller	no	Utstyrs-id
bim_name	controller	no	Utstyrsnavn
bim_type	controller	no	Utstyrstype
guid	controller	no	GUID
view_documents_for_procedure	controller	no	Tilknyttede dokumenter
document title	controller	no	Dokumentets tittel
document name	controller	no	Filnavn
document description	controller	no	Beskrivelse
select value	controller	no	Velg
component_for_control_group	controller	no	Utstyr tilknyttet kontrollgrupper
view_component_for_control_group	controller	no	Vis knytning mot utstyr
add_component_for_control_group	controller	no	Legg til knytning mot utstyr
show_controls_for_location	controller	no	Vis lokasjoner/ komponenter
property name	controller	no	Lokasjonsnavn
address	controller	no	Adresse
zip code	controller	no	Postnummer
no category selected	controller	no	Kategori ikke valgt
repeat_type_none	controller	no	Ingen
repeat_type_day	controller	no	Dag
repeat_type_week	controller	no	Uke
repeat_type_month	controller	no	Måned
repeat_type_year	controller	no	År
components for control	controller	no	Komponenter tilknyttet kontroll
add components for control	controller	no	Legg til knytning mellom kontroll og komponent(er)
locations for control	controller	no	Lokasjoner tilknyttet kontroll
location category	controller	no	Lokaliseringskategori
select add	controller	no	Velg for å legge til
select delete	controller	no	Velg for sletting
error_msg_1	controller	no	Vennligst fyll inn dette feltet
error_msg_2	controller	no	Vennligst velg en verdi i listen
error_msg_3	controller	no	Vennligst angi sluttdato etter startdato
error_msg_4	controller	no	Sjekklisten må være tilknyttet en kontroll
error_msg_5	controller	no	Vennligst angi når kontrollen ble utført
error_msg_6	controller	no	Kontrollen må være knyttet mot en komponent/lokasjon
error_msg_7	controller	no	Vennligst endre status for kontroll eller angi planlagtdato
error_msg_8	controller	no	Planlagtdato kan ikke være etter fristdato
error_msg_9	controller	no	Utførtdato kan ikke være etter fristdato
error_msg_no_controls_in_period	controller	no	Ingen kontroller for bygg i denne perioden
error_msg_no_controls_for_component	controller	no	Ingen kontroller for komponent i denne perioden
error_msg_control_passed_due_date	controller	no	Sjekkliste kan ikke lagres da frist er overskredet
role at location	controller	no	Tildeling av rolle
register new message	controller	no	Registrer ny melding
show message	controller	no	Vis melding
planned date	common	no	Planlagt Dato
status components	controller	no	Status komponenter
status locations	controller	no	Status lokasjoner
controle time	common	no	Kontrolltid
service time	common	no	Servicetid
total time	common	no	Totaltid
request ical event	controller	no	Send møteinnkalling
summary	common	no	Sammendrag
components	controller	no	Komponenter
report type	controller	no	Rapporttype
lang_control_mandatory_location	controller	no	Påkrevd lokasjonsvalg
control types	controller	no	Kontrolltyper
location	controller	no	Lokasjon
control_registered	controller	no	Kontroll satt opp
control_planned	controller	no	Kontroll har planlagt dato
control_done_over_time_without_errors	controller	no	Kontroll gjennomført uten åpne saker etter frist
control_done_in_time_without_errors	controller	no	Kontroll gjennomført uten åpne saker før frist
control_done_with_errors	controller	no	Kontroll gjennomført med åpne saker
control_not_done	controller	no	Kontroll ikke gjennomført
control_canceled	controller	no	Kontroll kansellert
document types	controller	no	Dokumenttyper
do not edit archived version	controller	no	Du kan ikke endre arkiverte versjoner
choose a location	controller	no	Velg en lokasjon
deadline end of year	controller	no	Frist på slutten av året for årskontroller
missing start date	controller	no	Mangler startdato
new revision	controller	no	Ny revisjon
deviation	controller	no	Avvik
save check list	controller	no	Lagre verdier
plan	controller	no	Planlegg
modified date	controller	no	Dato siste endring
modified by	controller	no	Endret av
set status: done	controller	no	Sett status: utført
in queue	controller	no	I kø
day	rental	no	Dag
hour	rental	no	Time
1-6 characters	rental	no	1-6 tegn
6 characters	rental	no	6 tegn
account_in	rental	no	Art/konto inntektsside
account_in_not_numeric	rental	no	Art/konto inntektsside må være et tall
account_number	rental	no	Kontonummer
account_out	rental	no	Art/konto utgiftsside
account_out_not_numeric	rental	no	Art/konto utgiftsside må være et tall
active_party	rental	no	Kontraktspart er aktiv
active_plural	rental	no	Aktive
active_single	rental	no	Aktiv
active	rental	no	Aktiv
add_area	rental	no	Legg til areal
added_areas	rental	no	Inkludert areal
add	rental	no	Legg til
address	rental	no	Adresse
adjust_price	rental	no	Juster pris
adjustment	rental	no	Regulering
adjustable	rental	no	Regulerbar
adjustment_interval	rental	no	Reguleringsintervall
adjustment_is_executed	rental	no	Reguleringen er utført
adjustment_is_not_executed	rental	no	Reguleringen er ikke utført
adjustment_list	rental	no	Reguleringer
adjustment_list_out_of_date	rental	no	Det er kjørt en nyere regulering for samme utvalg av kontrakter
adjustment_share	rental	no	Reguleringsandel
adjustment_type	rental	no	Reguleringstype
adjustment_year	rental	no	Sist regulert
adjustment_date	rental	no	Reguleringsdato
adjustment_type_kpi	rental	no	KPI
adjustment_type_deflator	rental	no	Kommunal deflator
advance	rental	no	Forskudd
agresso_id	rental	no	Agresso-ID
agresso_gl07	rental	no	Agresso GL07 - hovedbokstall
agresso_lg04	rental	no	Agresso LG04 - salgsordrer
add_location	rental	no	Legg enhet til leieobjektet
address1	rental	no	Adresse 1
address2	rental	no	Adresse 2
all	rental	no	Alle
alle	rental	no	Alle
all_locations	rental	no	Enheter fra eiendomsregisteret
and	rental	no	og
annually	rental	no	Årlig
area_gros	rental	no	Bruttoareal
area_net	rental	no	Nettoareal
area_not_found	rental	no	Kunne ikke finne detaljer om arealet
area	rental	no	Areal
area decimal places	rental	no	Antall desimaler for arealer
area suffix	rental	no	Areal suffix
area_max	rental	no	Max areal
audience	rental	no	Målgrupper
availability	rental	no	Status
availability_date	rental	no	Dato
available_areas	rental	no	Tilgjenglig areal
available_at	rental	no	Ledig på dato
available_composites	rental	no	Ledige leieobjekt
composites	rental	no	Leieobjekt
available_from	rental	no	Tilgjengelig fra
available_parties	rental	no	Tilgjengelige kontraktsparter
available_price_items	rental	no	Tilgjengelige priselementer
available?	rental	no	Kan leies ut?
available	rental	no	Tilgjengelig
available ?	rental	no	Tilgjengelig
available_for_pick	rental	no	tilgjengelig
back	rental	no	Tilbake
bank_guarantee	rental	no	Bankgaranti
billing date	rental	no	Fakturadato
billing time limit	rental	no	Faktura - tidsgrense
billing_external	rental	no	Ekstern
billing_internal	rental	no	Intern
billing_start	rental	no	Fakturastart
billing_end	rental	no	Fakturastopp
billing_term	rental	no	Termin
billing_terms	rental	no	Terminer
billing_removed_kf_contract	rental	no	Fjernet KF-kontrakt med id
billing_removed_contract_part_1	rental	no	Fjernet kontrakt
billing_removed_contract_part_2	rental	no	med total pris lik 0 kroner
billing_removed_external_contract	rental	no	Kontrakt med ansvarsområde eksternleie må ha kontrakttype
bill	rental	no	Faktura
bill2	rental	no	Fakturér
btn_add	rental	no	Deleger tilgang til bruker
btn_search	rental	no	Finn bruker
building	rental	no	Bygg
calculations_internal_investment	rental	no	Beregningsgrunnlag internleie/investeringer
calculate_price_apiece	rental	no	Pris regnes per stk.
calculate_price_per_area	rental	no	Pris regnes ut fra areal
calendar_months	rental	no	["Januar","Februar","Mars","April","Mai","Juni","Juli","August","September","Oktober","November","Desember"]
calendar_weekdays	rental	no	["Sø","Ma","Ti","On","To","Fr","Lø"]
cancel	rental	no	Avbryt
category config move in	rental	no	Kategori for innflyttingsmeldinger
category config move out	rental	no	Kategori for utflyttingsmeldinger
close	rental	no	Lukk
clear	rental	no	Nullstill
closing_due_date	rental	no	Nær opsjonsfrist
csv	rental	no	CSV
comment	rental	no	Kommentar
comments	rental	no	Kommentarer
commit	rental	no	Avslutt
commited	rental	no	Avsluttet
company	rental	no	Foretak
company_name	rental	no	Foretak
composite_name	rental	no	Navn på leieobjekt
composite_address	rental	no	Adresse på leieobjekt
composite	rental	no	Leieobjekt
composite_back	rental	no	Leieobjektsliste
composite_has_contract	rental	no	Leieobjekt med aktive kontrakter
composite_has_no_contract	rental	no	Leieobjekt uten aktive kontrakter
composite standard	rental	no	Leieobjekt standard
factor	common	no	Faktor
contract	rental	no	Kontrakt
contract_back	rental	no	Kontraktsliste
contract_id	rental	no	Kontraktsnummer
contract_not_adjustable	rental	no	Kontrakten er ikke regulerbar
contract_number	rental	no	Kontraktnummer
contract_warning	rental	no	Advarsler
contracts_containing_this_composite	rental	no	Kontrakter knyttet til dette leieobjektet
contracts	rental	no	Kontrakter
contracts_under_dismissal	rental	no	Kontrakter under avslutning
contract_under_dismissal	rental	no	Under avslutning
contract_notifications	rental	no	Kontraktens varsler
contract_notification_status	rental	no	Status
contracts_for_regulation	rental	no	Regulering med tilhørende kontrakter
contract_regulation_back	rental	no	Regulering
contracts_removed	rental	no	Kontrakter som er fjernet fra fakturakjøringen
contracts_with_one_time	rental	no	Kontrakter med engangsbeløp
contracts_in_cycle	rental	no	Kontrakter som følger vanlig fakturasyklus
contracts_out_of_cycle	rental	no	Kontrakter som avviker fra vanlig faktureringssyklus
contracts_not_billed_before	rental	no	Kontrakter som ikke er fakturert tidligere
contract_status	rental	no	Status
contract_type_eksternleie	rental	no	Eksternleie
contract_type_eksternleie_feste	rental	no	Feste (1520)
contract_type_eksternleie_leilighet	rental	no	Leilighet (1530)
contract_type_eksternleie_annen	rental	no	Annen (1510)
contract_type_innleie	rental	no	Innleie
contract_type_internleie	rental	no	Internleie
contract_type_internleie_egne	rental	no	Egne
contract_type_internleie_innleie	rental	no	Innleie
contract_type_internleie_investeringskontrakt	rental	no	Investeringskontrakt
contract_type_internleie_kf	rental	no	KF
contract_type_internleie_andre	rental	no	Andre
contract_type_investeringskontrakt	rental	no	Investeringskontrakt
contract_type	rental	no	Kontrakttype
contract_type_id	rental	no	Kontrakttype
contract_types	rental	no	Kontrakttyper
contract_type_internleie_1	rental	no	Utført internleieregulering av kontrakter som reguleres hvert år
contract_type_internleie_2	rental	no	Utført internleieregulering av kontrakter som reguleres hvert annet år
contract_type_internleie_10	rental	no	Utført internleieregulering av kontrakter som reguleres hvert tiende år
contract_type_innleie_1	rental	no	Utført innleieregulering av kontrakter som reguleres hvert år
contract_type_innleie_2	rental	no	Utført innleieregulering av kontrakter som reguleres hvert annet år
contract_type_innleie_10	rental	no	Utført innleieregulering av kontrakter som reguleres hvert tiende år
contract_type_eksternleie_1	rental	no	Utført eksternleieregulering av kontrakter som reguleres hvert år
contract_type_eksternleie_2	rental	no	Utført eksternleieregulering av kontrakter som reguleres hvert annet år
contract_type_eksternleie_10	rental	no	Utført eksternleieregulering av kontrakter som reguleres hvert tiende år
contracts_closing_due_date	rental	no	Kontrakter nær opsjonsfrist
contract_future_info	rental	no	Fremtidig kontraktsinformasjon
could not find specified billing job.	rental	no	Kunne ikke finne spesifisert fakturering.
count decimal places	rental	no	Antall desimaler for opptelling
count	rental	no	Antall
count_suffix	rental	no	stk
create_billing	rental	no	Opprett fakturakjøring
create_shortcut	rental	no	Ny snarvei
create_contract_contract_type_eksternleie	rental	no	Opprett eksternleiekontrakt
create_contract_contract_type_innleie	rental	no	Opprett innleiekontrakt
create_contract_contract_type_internleie	rental	no	Opprett internleiekontrakt
create_user_based_on_email_link	rental	no	Opprett bruker basert på e-post
create user based on email group	rental	no	Velg gruppe nye brukere blir innmeldt i
cs15_export	rental	no	Kundefil
currency decimal places	rental	no	Antall desimaler for valuta
currency prefix	rental	no	Valuta prefix
currency suffix	rental	no	Valuta suffix
currency_thousands_separator	rental	no	.
custom_address	rental	no	overstyrt
dashboard_title	rental	no	Forside - Min arbeidsoversikt
datatable_msg_empty	rental	no	Ingen data
datatable_msg_error	rental	no	Datafeil
datatable_msg_loading	rental	no	Laster data
date_end	rental	no	Gjelder til
date	rental	no	Dato
date_start	rental	no	Gjelder fra
decimal separator	rental	no	Skilletegn for desimaler
delegates	rental	no	Delegering
delegate_removed	rental	no	Delegaten ble fjernet.
delegation_error	rental	no	Feil under delegering
delegation_successful	rental	no	Velykket delegering
delete	rental	no	Slett
department	rental	no	Avdeling
deposit	rental	no	Depositum
description	rental	no	Beskrivelse
details	rental	no	Detaljer
document_type	rental	no	Dokumenttype
document_name	rental	no	Dokumentnavn
document_title	rental	no	Dokumenttittel
documents	rental	no	Dokumenter
download agresso import file	rental	no	Last ned Agresso-importfil (CS15)
download as %1	rental	no	Last tabellen i %1-format
download export	rental	no	Last ned eksporten
do_not_exist	rental	no	Eksisterer ikke
due_date	rental	no	Opsjonsfrist
economy	rental	no	Økonomi
edit_contract	rental	no	Redigér kontrakt
edit	rental	no	Redigér
elements_pr_page	rental	no	elementer per side
elements	rental	no	Elementer
email	rental	no	E-post
email_create_user_based_on_email_title	rental	no	Tilgang til Portico Estate
email_create_user_based_on_email_message	rental	no	Hei %1 %2:<br/>Det er opprettet en tilgang for deg i Portico Estate<br/><br/>Brukernavnet er e-posten din<br/>Passordet er %3<br/><br/>Systemet kan nås på adressen %4
ended	rental	no	Avsluttet
entity config move out	rental	no	Entitet for utflytting
entity config move in	rental	no	Entitet for innflytting
error_create_user_based_on_email	rental	no	En feil oppstod under opprettelse av bruker
error_create_user_based_on_email_account_exist	rental	no	En konto med denne e-posten som brukernavn eksisterer allerede
error_create_user_based_on_email_not_valid_address	rental	no	Kan ikke opprette kontoen pga av e-postadressen ikke er gyldig
error_no_contract_or_party	rental	no	Ingen kontrakt eller kontraktspart å utføre handlingen på
events	rental	no	Hendelser
every_second_week	rental	no	Hver 14. dag
excel	rental	no	Excel
execute_adjustments	rental	no	Utfør reguleringer
executive_officer	rental	no	Saksbehandler
executive_officer_for	rental	no	Saksbehandler for
export	rental	no	Eksport
export_contracts	rental	no	Eksporter kontrakter
export_contract_price_items	rental	no	Eksporter priselementer på kontrakter
export failed.	rental	no	Eksporten feilet.
export format	rental	no	Format
export generated.	rental	no	Eksporten ble generert.
export_to	rental	no	Eksporter
external	rental	no	Eksterne kontraktsparter
facilit_import	rental	no	Import fra Facilit
failed_removing_delegate	rental	no	Det oppstod en feil under sletting av delegaten.
fax	rental	no	Faks
fellesdata_not_in_use	rental	no	Fellesdata er ikke i bruk
field_of_responsibility	rental	no	Ansvarsområde
filters	rental	no	Filtre
finish	rental	no	Avslutt
fire_drawings	rental	no	Branntegninger
firstname	rental	no	Fornavn
first	rental	no	Første
first_half	rental	no	1. halvår
first_quarter	rental	no	1. kvartal
fixed	rental	no	Tidsbestemt
floor	rental	no	Etasje
fourth_quarter	rental	no	4. kvartal
f_new_contract	rental	no	Opprett kontrakt
f_new_party	rental	no	Ny kontraktspart
f_new_price_item	rental	no	Nytt priselement
f_new_rc	rental	no	Nytt leieobjekt
from	rental	no	Fra
from email setting	rental	no	E-post adresse systemmeldinger (Fra)
frontpage_was_reset	rental	no	Oppsettet på forsiden ble nullstilt
frontpage_reset_setup	rental	no	Nullstill oppsett
frontend_access	rental	no	Gå til frontend som valgt leietaker
f_select_columns	rental	no	Velg kolonner
furnish_type	rental	no	Møbleringsstatus
furnish_type_not_specified	rental	no	Ikke spesifisert
furnish_type_furnished	rental	no	Møblert
furnish_type_partly_furnished	rental	no	Delvis møblert
furnish_type_not_furnished	rental	no	Ikke møblert
gab	rental	no	GAB
gab_id	rental	no	GAB
generate export	rental	no	Generér eksport
generate cs15	rental	no	Generér kundefil
get_sync_data	rental	no	Hent data fra fellesdata
half-year	rental	no	Halvårig
hidden	rental	no	Skjult
hidden_for_pick	rental	no	skjult
has_custom_address	rental	no	Er adressen overstyrt?
house_number	rental	no	Husnummer
http address for external users	rental	no	HTTP adresse for eksterne brukere
id	rental	no	ID
identifier	rental	no	Identifikator
import_log_messages	rental	no	Import log
import_reset	rental	no	Nullstill importstatus
in_operation	rental	no	I drift
inactive_party	rental	no	Kontraktspart er inaktiv
inactive	rental	no	Inaktiv
included_units	rental	no	Leieobjektets enheter
internal	rental	no	Interne kontraktsparter
interval	rental	no	Intervall
invalid location code for the building.	rental	no	Ugyldig objektnummer for bygg.
invoice	rental	no	Faktura
invoice_run	rental	no	Fakturakjøring
invoice_menu	rental	no	Faktura
invoice_header	rental	no	Fakturaoverskrift
is_active	rental	no	Aktiv
is_area	rental	no	Areal
is_payer	rental	no	Fakturamottaker
is_inactive	rental	no	Inaktiv
is_adjustable	rental	no	Indeksreguleres
is_executed	rental	no	Utført
is_one_time	rental	no	Engangsbeløp
is_standard	rental	no	Standard priselement
job_title	rental	no	Stillingstittel
land_title	rental	no	Gnr/Bnr
lacking_username	rental	no	Brukernavn må fylles ut
last_edited_by_current_user	rental	no	Din siste endring
lastname	rental	no	Etternavn
last	rental	no	Siste
last_updated	rental	no	Sist oppdatert
level	rental	no	Nivå
link	rental	no	Lenke
location_code	rental	no	Enhetsnummer
location_id	rental	no	Intern organisasjonstilknytning
log_in_to_add_notfications	rental	no	Det er bare mulig å legge til varlser i redigeringsmodus.
make_pdf	rental	no	Lag PDF for utskrift
manual_adjust_price_item	rental	no	Manuell regulering av priselement
manual_adjust_price_item_select	rental	no	Velg priselement
manual_adjustment	rental	no	Manuell regulering
marked_as	rental	no	og er merket
max_area	rental	no	Areal
message	rental	no	Melding
messages_agresso_id_length	rental	no	Agresso-ID må inneholde 9 tall eller bokstaver
messages_fontpage_not_saved	rental	no	Oppsettet ble ikke lagret
messages_fontpage_saved	rental	no	Oppsettet ble lagret
messages_form_error	rental	no	Skjemaet inneholder en feil.
messages_general	rental	no	Feil i feltet
ssn	rental	no	Fødselsnummer
messages_isint	rental	no	Feltet må inneholde et heltall
messages_isnumeric	rental	no	Feltet må inneholde et tall
messages_new_composite	rental	no	Leieobjektet er opprettet
messages_new_contract	rental	no	Ny kontrakt lagt til
messages_new_contract_copied	rental	no	Ny kontrakt lagt til basert på kontrakt
messages_new_contract_from_composite	rental	no	Ny kontrakt lagt til basert på leieobjekt
messages_new_party	rental	no	Kontraktspart er opprettet
messages_not_valid_date	rental	no	Må være en gyldig dato
messages_number_out_of_range	rental	no	Tallet er over eller under tillatte verdier
messages_required_field	rental	no	Dette feltet er påkrevd
messages_right_click_to_add	rental	no	Høyreklikk for å legge til
messages_saved_form	rental	no	Informasjonen ble lagret.
messages_string_too_long	rental	no	Teksten er for lang
missing account in.	rental	no	Inngående konto mangler.
missing account out.	rental	no	Utgående konto mangler.
missing billing information.	rental	no	Ufullstendig konteringsinformasjon for kontrakt med id %1. Dette må rettes for å kunne fakturere kontrakten.
missing contract party.	rental	no	Kontraktspart mangler.
missing payer id.	rental	no	Mangler fakturamottaker.
missing project id.	rental	no	Prosjektnummer mangler.
missing responsibility id.	rental	no	Ansvar mangler.
missing service id.	rental	no	Tjeneste mangler.
missing system setting for project id.	rental	no	Systeminnstilling for prosjektnummer mangler.
missing system setting for responsibility id for the current user.	rental	no	Systeminnstilling for ansvar mangler.
missing_agresso_id	rental	no	Agresso Id må fylles ut
mobile_phone	rental	no	Mobiltelefon
month 1	rental	no	januar
month 2	rental	no	februar
month 3	rental	no	mars
month 4	rental	no	april
month 5	rental	no	mai
month 6	rental	no	juni
month 7	rental	no	juli
month 8	rental	no	august
month 9	rental	no	september
month 10	rental	no	oktober
month 11	rental	no	november
month 12	rental	no	desember
month 0 capitalized	rental	no	Ikke tilgjengelig
month 1 capitalized	rental	no	Januar
month 2 capitalized	rental	no	Februar
month 3 capitalized	rental	no	Mars
month 4 capitalized	rental	no	April
month 5 capitalized	rental	no	Mai
month 6 capitalized	rental	no	Juni
month 7 capitalized	rental	no	Juli
month 8 capitalized	rental	no	August
month 9 capitalized	rental	no	September
month 10 capitalized	rental	no	Oktober
month 11 capitalized	rental	no	November
month 12 capitalized	rental	no	Desember
monthly	rental	no	Månedlig
month	rental	no	Måned
name	rental	no	Navn
never	rental	no	Aldri
new_notification	rental	no	Nytt varsel
new_billing	rental	no	Ny fakturering
new_adjustment	rental	no	Ny regulering
new_price	rental	no	Ny pris
next	rental	no	Neste
no	rental	no	Nei
none	rental	no	Ingen
nobody	rental	no	Ingen
no billing jobs found	rental	no	Ingen faktureringer funnet
no_contracts_found	rental	no	Ingen kontrakter passet til søkekriteriene
no contracts were selected.	rental	no	Du må velge minst én kontrakt for å faktuere.
no_hits	rental	no	Ingen treff
no invoices were found	rental	no	Ingen fakturaer funnet
no_name_composite	rental	no	Leieobjekt uten navn (løpenummer: %1)
no_party_location	rental	no	Ingen intern organisasjonstilhørighet
no_value	rental	no	Ingen
not_available	rental	no	Ikke tilgjengelig
notification_status	rental	no	Varsel
notifications	rental	no	Varsler
notification_optgroup_groups	rental	no	Grupper
notification_optgroup_users	rental	no	Brukere
not_started	rental	no	Ikke startet
not_available_nor_hidden	rental	no	tilgjengelig eller skjult
object_number	rental	no	Objektnummer
objno_name_address	rental	no	Objektnummer/navn/adresse
occupied	rental	no	Opptatt
of_total	rental	no	av totalt
ok	rental	no	Ok
old_contract_id	rental	no	Gammelt kontraktsnummer
ods	rental	no	ODS
one or more price items are missing agresso ids.	rental	no	Ett eller flere priselementer mangler Agresso-id.
one or more price items have an invalid agresso id. id must consist of one capital letter and three digits.	rental	no	Ett eller flere priselementer har en ugyldig Agresso-id. Iden må bestå av én stor bokstav og tre tall.
only_one_time	rental	no	Status
only_one_time_yes	rental	no	Avviker fra vanlig faktureringssyklus, kun engangsbeløp
only_one_time_no	rental	no	Følger vanlig fakturasyklus
open_and_exported_exist	rental	no	En fakturakjøring som ikke er avsluttet med eksportert til Agresso-format eksisterer for dette ansvarsområdet. Denne må enten slettes eller avsluttes før denne fakturakjøringen kan eksportes.
organization	rental	no	Organisasjon
organisation_number	rental	no	Organisasjonsnummer
organisation_or_ssn_number	rental	no	Org./fødselsnr
orphan_units	rental	no	Ubrukte arealer
or	rental	no	eller
org_enhet_id	rental	no	Organisasjonsenhet
org_unit_name	rental	no	Navn på tilsvarende enhet i Fellesdata
org_unit_exist	rental	no	Eksisterer
other_guarantee	rental	no	Annen garanti
others	rental	no	Annet
other operations	rental	no	Andre operasjoner
out_of_operation	rental	no	Ikke i drift
overridden_address	rental	no	Overstyrt adresse
override	rental	no	Fakturer fra
panels	rental	no	Vinduer
part_of_contract	rental	no	Inngår i kontrakt av typen
parties	rental	no	Kontraktsparter
party_name	rental	no	Navn til kontraktspart
party	rental	no	Kontraktspart
party_back	rental	no	Kontraktspartliste
party_location	rental	no	Intern organisasjonstilhørighet
party_type	rental	no	Type kontraktspart
payer_id	rental	no	Fakturamottaker
percent	rental	no	Prosent
period	rental	no	Periode
permission_denied_edit_contract	rental	no	Du mangler rettigheter for å legge til å redigere kontrakten
permission_denied_new_contract	rental	no	Du mangler rettigheter for å legge til å opprette nye kontrakter
permission_denied_view_contract	rental	no	Du mangler rettigheter for å legge til å se kontrakten
permission_denied_add_document	rental	no	Du mangler rettigheter for å legge til et dokumenent
phone	rental	no	Telefon
postal_code_place	rental	no	Postnummer/-sted
post_code	rental	no	Postnummer
post_place	rental	no	Sted
postal_code	rental	no	Postnummer
place	rental	no	Sted
previous	rental	no	Forrige
price_item	rental	no	Priselement
price_item_id	rental	no	Priselement ID
price_item_type_apiece	rental	no	Stk.
price_item_type_area	rental	no	Areal
price_item_inactive	rental	no	Inaktiv
price_item_active	rental	no	Aktiv
price_item_adjustable	rental	no	Ja
price_item_not_adjustable	rental	no	Nei
price_list	rental	no	Prisbok
price_per_unit	rental	no	Pris pr kvm
price	rental	no	Pris
price_element_in_use	rental	no	Priselementet er i bruk på en aktiv kontrakt
project_id	rental	no	Prosjektnummer
project id can not be more than 6 characters.	rental	no	Prosjektnummer kan ikke være mer enn seks tegn.
propertyident	rental	no	G.nr. / B.nr. / F.nr. / S.nr.
property_id	rental	no	BKB Identifikator
property	rental	no	Eiendom
publish_comment	rental	no	Kommentar vises i frontend
quarterly	rental	no	Kvartalvis
rc	rental	no	Leieobjekter
recurrence	rental	no	Gjentakelse
reference	rental	no	Deres ref
regulation	rental	no	Regulering
regulation_back	rental	no	Reguleringsliste
related_delegates	rental	no	Delegater
remove	rental	no	Fjern
remove_from_workbench	rental	no	Fjern fra mitt skrivebord
remove_from_all_workbenches	rental	no	Fjern fra alle skrivebord
remove_location	rental	no	Fjern enhet fra leieobjektet
rental_composite	rental	no	Leieobjekt
rental	rental	no	Leie
rented_area	rental	no	Utleid areal
rented_area_not_numeric	rental	no	Areal må være et tall
reports	rental	no	Rapporter
reset_price_item	rental	no	Hent verdier fra prisboken
reset	rental	no	Nullstill
reskontro	rental	no	Reskontro
responsibility	rental	no	Ansvar
responsibility_id	rental	no	Ansvar
responsibility id must be 6 characters.	rental	no	Ansvar må være seks tegn.
responsibility_id_not_numeric	rental	no	Ansvar må være et tall
result_unit_back	rental	no	Resultatsliste
result_unit	rental	no	Resultatsenhet
result_unit_number	rental	no	Resultatsenhet
run	rental	no	Kjørt
run by	rental	no	Utført av
room	rental	no	Rom
running	rental	no	Løpende
save	rental	no	Lagre
save_setup	rental	no	Lagre oppsett på forsiden
search_for	rental	no	Søk etter
search_options	rental	no	Søkevalg
search option	rental	no	Søkevalg
search	rental	no	Søk
search_where	rental	no	i
second_half	rental	no	2. halvår
second_quarter	rental	no	2. kvartal
section	rental	no	Seksjon
security	rental	no	Sikkerhet
security_amount	rental	no	Sikkerhetsbeløp
security_amount_not_numeric	rental	no	Sikkerhetsbeløp må være et tall
select_all_options	rental	no	&lt;Alle$gt;
select_date	rental	no	Velg dato
select_date_valid_year	rental	no	Vennligst velg et gyldig år
selected_composites	rental	no	Valgte leieobjekter
selected_parties	rental	no	Valgte kontraktsparter
selected_price_items	rental	no	Valgte priselementer
serial	rental	no	Løpenummer
serial start	rental	no	Start - ordrenummerserie
serial stop	rental	no	Slutt - ordrenummerserie
service	rental	no	Tjeneste
service_id	rental	no	Tjeneste
service id must be 5 characters.	rental	no	Tjeneste må være fem tegn.
service_id_not_numeric	rental	no	Tjeneste må være et tall
service_exist	rental	no	Eksisterer
set_payer	rental	no	Sett som fakturamottaker
shortcuts	rental	no	Snarveier
show_affected_contracts	rental	no	Vis kontrakter relatert til reguleringen
show_move_in_reports	rental	no	Vis jasperreport for innflyttingsmelding
show_move_out_reports	rental	no	Vis jasperreport for utflyttingsmelding
show_in_out_move_reports	rental	no	Vis jasperreports for inn- og utflyttingsmeldinger
showing_composite	rental	no	Leieobjekt
showing_contract	rental	no	Kontrakt
showing	rental	no	Viser priselement
show	rental	no	Vis
shows_from	rental	no	Viser fra
started	rental	no	Startet
status_before	rental	no	før
status_date	rental	no	den
status_unknown	rental	no	Ukjent status
status	rental	no	Status
success	rental	no	Suksess
success_create_user_based_on_email	rental	no	En brukerkonto ble opprettet og en e-post ble sendt til brukeren
sum	rental	no	Sum
sync	rental	no	Synkroniser
sync_identifier	rental	no	Kun identifikator
sync_menu	rental	no	Synkronisering
sync_message	rental	no	Melding
sync_org_unit	rental	no	Eksisterende kobling
sync_resp_and_service	rental	no	Ansvar
sync_res_units	rental	no	Gammelt resultatenhetsnummer
sync_parties	rental	no	Synkronisér kontraktsparter mot Fellesdata
sync_parties_service_and_responsibiity	rental	no	Synkronisering: Ansvar (Kontrakt)
sync_parties_result_unit_number	rental	no	Synkronisering: Resultatenhetsnummer (Kontraktspart)
sync_parties_identifier	rental	no	Synkronisering: Identifikator
sync_parties_fellesdata_id	rental	no	Synkronisering: Organisasjonsidentifikator
sync_org_name_fellesdata	rental	no	Foretak i Fellesdata
sync_org_email_fellesdata	rental	no	Epost i Fellesdata
sync_org_unit_leader_fellesdata	rental	no	Enhetsleder i Fellesdata
sync_org_department_fellesdata	rental	no	Avdeling i Fellesdata
syncronize_party	rental	no	Synkroniser kontraktspart
system setting for project id can not be more than 6 characters.	rental	no	Systeminnstilling for prosjektnummer kan ikke være mer enn seks tegn.
system setting for responsibility id for the current user must be 6 characters.	rental	no	Systeminnstilling for ansvar må være seks tegn.
target_me	rental	no	Meg selv
target_none	rental	no	Ingen
terminated_contract	rental	no	Under opphør
terminated_contracts	rental	no	Opphørte kontrakter
the period has been billed before.	rental	no	Denne kontrakttypen med denne faktureringsterminen har allerede blitt fakturert for denne perioden.
thousands separator	rental	no	Skilletegn for tusen
third_quarter	rental	no	3. kvartal
t_functions	rental	no	Funksjoner
title	rental	no	Tittel
t_new_composite	rental	no	Opprett nytt leieobjekt
t_new_contract	rental	no	Ny kontrakt
t_new_party	rental	no	Opprett ny kontraktspart
t_new_price_item	rental	no	Opprett nytt priselement
to	rental	no	Til
to_the_top	rental	no	Til toppen
total sum	rental	no	Totalt beløp
total_price	rental	no	Total pris
total_price_current_year	rental	no	Pris i budsjettperiode
type	rental	no	Type
under_dismissal	rental	no	Under oppsigelse
under_planning	rental	no	Under planlegging
units	rental	no	Enheter
unit_id	rental	no	Enhetsid
unit_name	rental	no	Enhetsnavn
unit_leader_name	rental	no	Navn enhetsleder
unit_leader	rental	no	Enhetsleder
unit_no_of_delegates	rental	no	Antall delegater
update	rental	no	Oppdatér
upload	rental	no	Last opp
url	rental	no	Nettsted
unable to get a location code for the building.	rental	no	Klarte ikke å hente objektnummer for bygget.
unit_leader2	rental	no	Leder avdeling/enhet
username	rental	no	Brukernavn
user_or_group	rental	no	Bruker/Gruppe
vacant	rental	no	Ledig
view_contract	rental	no	Vis kontrakt
unable_to_connect_to_database	rental	no	Problemer med å koble til databasen.
unknown_user	rental	no	Personen finnes ikke.
user_found_in_fellesdata	rental	no	Personen finnes i Fellesdata.
user_not_in_fellesdata	rental	no	Personen finnes ikke i Fellesdata.
user_found_in_pe	rental	no	Personen er bruker av dette systemet.
warning_billing_date_between	rental	no	Fakturastart må være innenfor kontraktens datoer!
warning_billing_end_date_between	rental	no	Fakturastopp må være innenfor kontraktens datoer!
warning_due_date_between	rental	no	Opsjonsfrist må være innenfor kontraktens datoer!
warning_lacking_start_date	rental	no	Kontrakten mangler start-dato!
warning_price_item_date_between	rental	no	Priselementets datoer må være innenfor kontraktens datoer!
weekly	rental	no	Ukentlig
working_on	rental	no	Kontrakter under arbeid
year	rental	no	År
yes	rental	no	Ja
your_notifications	rental	no	Dine varsler
download excel export	rental	no	Last ned eksportfil i Excel-format
free_of_charge	rental	no	Vederlagsfritt
extra_adjustment	rental	no	Ekstra regulering
select	rental	no	Velg
district	rental	no	Område
list %1	rental	no	Alle %1
new	rental	no	Ny
download excel export bk	rental	no	Last ned Excel-eksport BK
serial_number	rental	no	Serienummer
name or company is required	rental	no	Navn på person ELLER navn på foretak er obligatorisk
simulation	rental	no	Simulering
credits	rental	no	Kreditering
override adjustment start	rental	no	Overstyr sist regulert
application	rental	no	søknad
dimb	rental	no	Ansvarssted
payment method	rental	no	Betalingsmetode
what	rental	no	Hva
cleaning	rental	no	Rengjøring
assignment	rental	no	Tildeling
assign_start	rental	no	Tildelt fra
assign_end	rental	no	Tildelt til
registered	rental	no	Registrert
pending	rental	no	Under behandling
rejected	rental	no	Avvist
approved	rental	no	Godkjent
location	rental	no	Lokalisering
custom price factor	rental	no	Prisfaktor
standard	rental	no	Standard
composite type	rental	no	Leieobjekt type
custom price	rental	no	Taksering
price type	rental	no	Pristype
schedule	rental	no	Kalender
moveout	rental	no	Utflytting
movein	rental	no	Innflytting
custom fields	rental	no	Egendefinerte felt
custom field groups	rental	no	Grupper for egendefinerte felt
report	rental	no	Rapport
basis data	rental	no	Grunnlagsdata
created	rental	no	Registrert
modified	rental	no	Endret
picture	rental	no	Bilde
delete file	rental	no	Slett fil
user	rental	no	Bruker
note	rental	no	Merknad
report is already recorded for %1	rental	no	Rapport finnes allerede for %1
email out	rental	no	Epost ut
recipient	rental	no	Mottaker
recipients	rental	no	Mottakere
candidates	rental	no	Kandidater
send email	rental	no	Send epost
remark	rental	no	Merknad
subject	rental	no	Overskrift
content	rental	no	Innhold
select all	rental	no	Velg alle
planned	rental	no	Planlagt
email template	rental	no	Standardtekster
expired	rental	no	Utgått
positive one time	rental	no	Positive engangsbeløp
add type	rental	no	Legg til type
add unit	rental	no	legg til enhet
contact phone	rental	no	Kontakttelefon
data has been saved	rental	no	Data er lagret
document has been added	rental	no	Dokument er lagt til
edit unit	rental	no	Endre enhet
entity %1 has been updated	rental	no	Post %1 er oppdatert
event	rental	no	Hendelse
export contacts	rental	no	Eksporter kontakter
has been added	rental	no	er lagt til
has been removed	rental	no	er fjernet
select date	rental	no	Velg dato
select file to upload	rental	no	Velg fil for opplasting
synchronized: %1	rental	no	Synkronisert: %1
syncronize all	rental	no	Synkroniser alle
customer id	rental	no	Kundenummer
customer order id	rental	no	Kundens ordrenr.
property name	rental	en	Property name
logged in as	frontend	no	Innlogget som
drawings	frontend	no	Tegninger
frontend	frontend	no	Frontend
pictures	frontend	no	Bilder
maintenance	frontend	no	Vedlikehold
refurbishment	frontend	no	Oppgraderinger
services	frontend	no	Tjenester
contract	frontend	no	Kontrakter
contract_in	frontend	no	Innleiekontrakter
contract_ex	frontend	no	Eksternleiekontrakter
contract_documents	frontend	no	Kontraktsdokumenter
helpdesk	frontend	no	Melding om avvik
subject	frontend	no	Meldingstittel
entry_date	frontend	no	Dato
status	frontend	no	Status
list ticket	frontend	no	Meldingsoversikt
locationdesc	frontend	no	Sted
description	frontend	no	Beskrivelse
file	frontend	no	Legg til dokument
missing field(s)	frontend	no	Alle felt må fylles ut
user	frontend	no	Meldt inn av
open	frontend	no	Åpen
ticket added	frontend	no	Avviksmelding registrert
opened	frontend	no	Åpnet
add ticket	frontend	no	Ny avviksmelding
apply	frontend	no	Lagre
priority changed	frontend	no	Prioritet endret
tab sorting	frontend	no	Fanesortering
frontend settings	frontend	no	Frontend-innstillinger
all	frontend	no	Vis alle
closed	frontend	no	Avsluttede
not_implemented	frontend	no	Ikke implementert
select_unit	frontend	no	Velg bygg
under_planning	frontend	no	Under planlegging
active_single	frontend	no	Aktiv
ended	frontend	no	Avsluttet
status_unknown	frontend	no	Ukjent status
old_contract_id	frontend	no	Kontraktsnummer
date_start	frontend	no	Startdato
date_end	frontend	no	Sluttdato
contract_status	frontend	no	Status
rented_area	frontend	no	Leid areal
total_price	frontend	no	Pris
service_id	frontend	no	Tjenestested
responsibility_id	frontend	no	Ansvarssted
logout	frontend	no	Logg ut
help	frontend	no	Hjelp
contact_bkb	frontend	no	Kontakt EBE
folder	frontend	no	Informasjon internleie
number_of_units	frontend	no	Antall bygg
total_area	frontend	no	Totalt areal
total_area_internal	frontend	no	Totalt areal (internleie)
total_price_internal	frontend	no	Total pris (internleie)
square_meters	frontend	no	kvm
currency	frontend	no	kr
contracts_not_included_in_totals	frontend	no	Innleiekontrakter er ikke tatt med i totalt areal og pris
organisational_units	frontend	no	Resultatenheter
all_organisational_units	frontend	no	Alle enheter
chosen_unit	frontend	no	Valgt bygg
choose_contract	frontend	no	Velg kontrakt
no_name_organisational_unit	frontend	no	Resultatenheten har ingen navn
no_name_unit	frontend	no	--Ingen navn--
new_ticket	frontend	no	Skriv ny melding
contract_type_eksternleie	frontend	no	Eksternleie
contract_type_eksternleie_feste	frontend	no	Feste (1520)
contract_type_eksternleie_leilighet	frontend	no	Leilighet (1530)
contract_type_eksternleie_annen	frontend	no	Annen (1510)
contract_type_innleie	frontend	no	Innleie
contract_type_internleie	frontend	no	Internleie
contract_type_internleie_egne	frontend	no	Egne
contract_type_internleie_innleie	frontend	no	Innleie
contract_type_internleie_investeringskontrakt	frontend	no	Investeringskontrakt
contract_type_internleie_kf	frontend	no	KF
contract_type_internleie_andre	frontend	no	Andre
contract_type_investeringskontrakt	frontend	no	Investeringskontrakt
contract_type	frontend	no	Kontraktsområde / type
no_end_date	frontend	no	Ingen
no_contracts	frontend	no	Ingen kontrakter
no_contract_details	frontend	no	Ingen konktraktsdetaljer tilgjengelig
ingen	frontend	no	Ingen
contract_internal	frontend	no	Internleiekontrakter
active	frontend	no	Aktive
not_active	frontend	no	Avsluttede
find_user	frontend	no	Finn bruker
username	frontend	no	Brukernavn
firstname	frontend	no	Fornavn
lastname	frontend	no	Etternavn
no_hits	frontend	no	Ingen treff
no_delegates	frontend	no	Ingen delegater
btn_add	frontend	no	Deleger tilgang til bruker
btn_search	frontend	no	Finn bruker
btn_remove	frontend	no	Fjern myndighet
btn_send	frontend	no	Send melding
send_contract_message	frontend	no	Send melding til EBE angående kontrakten
user_found_in_fellesdata	frontend	no	Personen finnes i Fellesdata.
user_found_in_pe	frontend	no	Personen er bruker av dette systemet.
user_not_found_in_pe	frontend	no	Personen er IKKE bruker av dette systemet.
delegation_successful	frontend	no	Velykket delegering
delegation_error	frontend	no	Feil under delegering
max_x_delegates	rental	no	Det er en øvre grense på %1 delegater pr leder
delegate limit	frontend	no	Maks-delegater
delegates	frontend	no	Delegering
no_buildings	frontend	no	Ingen bygninger tilknyttet resultatenheten
title_contract_message	frontend	no	Melding angående kontrakt
show_all_tickets	frontend	no	Vis alle avviksmeldinger på bygget
of	frontend	no	av
vendor	frontend	no	Leverandør
assigned_to	frontend	no	Tildelt
contact	frontend	no	Kontakt
message	frontend	no	Melding
comments	frontend	no	Kommentarer
on	frontend	no	den
messages	frontend	no	Innboks
no_new_messages	frontend	no	Innboks (0)
home	frontend	no	Hjem
delegates_for_res_unit	frontend	no	Delegater for valgt resultatenhet
delegates_for_user	frontend	no	Dine delegater uavhengig av resultatenhet
deletage_to_all_res_units	frontend	no	Myndighet vil bli gitt til alle resultateneheter du er leder for
email_create_account_title	common	no	BKBygg systemtilgang
email_create_account_message	common	no	Systemmelding fra BKBygg til %1 %2:<br/>Det er opprettet en tilgang for deg i BKBygg<br/><br/>Du får tilgang til systemet via kommunens intranett.<br/>Under verktøy i høyre kolonne, velg BKBygg.<br/>Brukerveiledning finner du i systemet, ved å velge "Hjelp" i toppmenyen.<br/>Har du spørsmål send en e-post til BKBygg brukerstøtte.<br/>
email_remove_delegate_title	common	no	BKBygg systemtilgang
email_remove_delegate_message	common	no	Systemmelding fra BKBygg til %1 %2:<br/>Din tilgang til BKBygg på vegne av %3 %4 er slettet.<br/>Har du spørsmål send en e-post til BKBygg brukerstøtte.<br/>
email_add_delegate_title	common	no	BKBygg systemtilgang
email_add_delegate_message	common	no	Systemmelding fra BKBygg til %1 %2:<br/>%3 %4 har gitt deg tilgang til BKBbygg for følgende resultatenhet:<br/>%5<br/><br/>Du får tilgang til systemet via kommunens intranett.<br/>Under verktøy i høyre kolonne, velg BKBygg.<br/>Brukerveiledning finner du i systemet, ved å velge "Hjelp" i toppmenyen.<br/>Har du spørsmål send en e-post til BKBygg brukerstøtte.<br/>
error_delegating_unit	frontend	no	En feil oppstod når det skulle under delegering til resultatenhet: %1
searching_for_self	frontend	no	Vennligst søk etter et annet brukernavn enn ditt eget
remove_delegate_successful	frontend	no	Delegaten ble fjernet
remove_delegate_error	frontend	no	Fjerning av delegaten var mislykket
message_empty	frontend	no	Meldingen kan ikke være tom
upload_userdoc	frontend	no	Last opp brukerveiledning
filename	frontend	no	Filnavn
contracts	frontend	no	Kontrakt
max %1 delegates	frontend	no	Maximum %1 delegerte
monthly	frontend	no	Månedlig
remark	frontend	no	Merknad
mobilefrontend	common	no	Mobil Frontend
mobilefrontend	common	en	Mobile Frontend
account active	admin	no	Bruker er aktiv
account "%1" properties	admin	no	Bruker "%1" egenskaper
active	admin	no	Aktiv
all records and account information will be lost!	admin	no	All historie og brukerinformasjon vil gå tapt!
anonymous user	admin	no	Anonym bruker
are you sure you want to delete this account ?	admin	no	Er du sikker på at du vil slette denne account?
are you sure you want to delete this group ?	admin	no	Er du sikker på du vil slette denne gruppen?
are you sure you want to kill this session ?	admin	no	Er du sikker på at du vil avslutte denne session?
create group	admin	no	Lag Gruppe
delete message	admin	no	Slett Melding
disabled	admin	no	Deaktivert
disabled (not recomended)	admin	no	Deaktivert (ikke anbefalt)
fallback (after each pageview)	admin	no	Fallback (etter hver sidevisning)
display	admin	no	Vis
global message	admin	no	Global Melding
group name	admin	no	Gruppenavn
idle	admin	no	idle
ip	admin	no	IP
kill	admin	no	Avslutt
last time read	admin	no	Lest siste gang
last %1 logins	admin	no	Siste %1 logins
list of current users	admin	no	liste over brukere
login time	admin	no	Login Tid
loginid	admin	no	Brukernavn
manager	admin	no	Manager
message	admin	no	Melding
new group name	admin	no	Nytt gruppenavn
new password [ leave blank for no change ]	admin	no	Nytt passord [ Ingenting hvis ingen forandring ]
percent of users that logged out	admin	no	Prosent av brukere som logget ut
re-enter password	admin	no	Skriv inn passord igjen
site	admin	no	Site
that loginid has already been taken	admin	no	Den loginID er opptatt
the login and password can not be the same	admin	no	Loging og passord kan ikke være det samme
the two passwords are not the same	admin	no	Passordene er ikke de sammme
total records	admin	no	Total historie
user accounts	admin	no	Brukerkontoer
user groups	admin	no	Brukergrupper
view access log	admin	no	Vis Accesslog
view sessions	admin	no	Vis sesjoner
you must enter a password	admin	no	Du må skrive inn et passord
you must select a file type	admin	no	Du må velge en filtype
home screen message	admin	no	Melding på hjemmeskjerm
title	admin	no	Overskrift
important message	admin	no	Viktig informasjon
enabled	admin	no	Aktiv
(to install new applications use<br><a href="setup/" target="setup">setup</a> [manage applications] !!!)	admin	no	(For å installere nye moduler, bruk<br><a href="setup/" target="setup">setup</a> [manage applications] !!!)
add a category	admin	no	Legg til kategori
add a section	admin	no	Legg til seksjon
add a subcategory	admin	no	Legg til undekategori
admins	admin	no	Admins
appearance	admin	no	Utseende
attribute	admin	no	Egenskap
attributes	admin	no	Egenskaper
attributes for this config section	admin	no	Egenskaper for denne konfigurasjonsseksjonen
category list	admin	no	Kategoriliste
check ip address of all sessions	admin	no	Kontroller IP-adresse for sesjoner
close window	admin	no	Lukk vindu
collect missing translations	admin	no	Finn manglende oversettinger
config	admin	no	Konfigurasjon
delete all log records	admin	no	Slett alle poster fra loggen
delete this category	admin	no	Slett kategorien
edit the config	admin	no	Endre konfigurasjon
edit this category	admin	no	Endre kategorien
email domain	admin	no	E-post domene
enter the background color for the login page	admin	no	Angi bakgrunnsfarge for innloggingssiden
enter the background color for the site title	admin	no	Angi bakgrunnsfarge for nettstedstittelen
enter the file name of your logo	admin	no	Angi filnavnet for logo
enter the file name of your logo at login	admin	no	Angi filnavnet for logo for pålogging
enter the search string. to show all entries, empty this field and press the submit button again	admin	no	Angi søkestreng. For a vise alle poster, tøm dette feltet og klikk på knappen igjen.
enter the title for your site	admin	no	Angi tittel for nettstedet
enter the title of your logo	admin	no	Angi tittel for logoen
enter the url where your logo should link to	admin	no	Angi url for logo
enter your smtp server password	admin	no	Angi ditt SMTP passord
enter your smtp server user	admin	no	Angi ditt SMTP-server brukernavn
fatal	admin	no	Fatal
first page	admin	no	Første side
installed applications	admin	no	Innstallerte moduler
language	admin	no	Språk
line	admin	no	Linje
list section	admin	no	List seksjon
log message	admin	no	Loggmelding
login screen	admin	no	Innlogging
main screen	admin	no	Hovedside
main screen message	admin	no	Melding på hovedside
module	admin	no	Modul
section	admin	no	seksjon
security	admin	no	Sikkerhet
severity	admin	no	Alvorlighetsgrad
smtp server port number	admin	no	SMTP server port nummer
smtp server timeout	admin	no	smtp server timeout
smtp settings	admin	no	SMTP configurasjon
smtpdebug	admin	no	smtpdebug
smtpsecure	admin	no	smtpsecure
submit the search string	admin	no	Send søketekst
support email address	admin	no	E-post brukerstøtte
total records: %1	admin	no	Antall poster: %1
use cookies to pass sessionid	admin	no	Bruk cookier til å håndtere sesjoner
use smtp auth	admin	no	Bruk smtp autentisering
users	admin	no	Brukere
user	admin	no	Bruker
view the config	admin	no	Vis konfigurasjon
warn	admin	no	Varsel
add category	admin	no	Legg til kategori
edit category	admin	no	Endre kategori
parent category	admin	no	Foreldrekategori
add user	admin	no	Legg til bruker
add user account	admin	no	Legg til brukerkonto
action	admin	no	Handling
file	admin	no	Fil
info	admin	no	Informasjon
notice	admin	no	Merknad
hooks updated	admin	no	Applikasjonskoblinger er oppdatert
the new hooks should be available to all users	admin	no	Nye applikasjonskoblinger er tilgjengelig for alle brukere
async services last executed	admin	no	Asynkrone servicer ble sist utført
run asynchronous services	admin	no	Kjør asynkrone servicer
asyncservices not yet installed or other error (%1) !!!	admin	no	Asynkrone servicer er ikke installert, eller annen feil (%1) !!!
crontab only (recomended)	admin	no	Bare crontab (anbefalt)
installed crontab	admin	no	Installert crontab
install crontab	admin	no	Installer crontab
for the times below (empty values count as '*', all empty = every minute)	admin	no	For de tidene under (tomme verdier teller som '*', hvis alle er tom = hvert minutt)
year	admin	no	År
month	admin	no	Måned
day of week (0-6, 0=sun)	admin	no	Ukedag (0-6, 0=Søn)
hour	admin	no	Time
minute	admin	no	Minutt
calculate next run	admin	no	Kalkuler neste kjøring
enable debug-messages	admin	no	Aktiver feilsøkingsmeldinger
cancel testjob!	admin	no	Kanseller testjobb!
start testjob!	admin	no	Start testjobb!
for the times above	admin	no	For tidene over
the testjob sends you a mail everytime it is called	admin	no	Testjobben sender deg en e-post hver gang den blir kjørt
jobs	admin	no	Jobber
next run	admin	no	Neste kjøring
times	admin	no	Tider
update	admin	no	Oppdater
manual run	admin	no	Manuell kjøring
method	admin	no	Metode
data	admin	no	Data
settings	admin	no	Innstillinger
color selector	admin	no	Farge velger
would you like to check for a new version when admins login	admin	no	Vil du sjekke om det finnes ny versjon når administratorer logger inn
please set a site name in admin &gt; siteconfig	common	no	Vennligst angi tittelen for systemet i Administrasjon &gt; Admin &gt; Global konfigurasjon
%1 - %2 of %3 user accounts	admin	en	%1 - %2 of %3 user accounts
%1 not found or not executable !!!	admin	en	%1 not found or not executable !!!
(stored password will not be shown here)	admin	en	(Stored password will not be shown here)
(to install new applications use<br><a href="setup/" target="setup">setup</a> [manage applications] !!!)	admin	en	(To install new applications use<br><a href="setup/" target="setup">Setup</a> [Manage Applications] !!!)
accesslog and bruteforce defense	admin	en	AccessLog and BruteForce defense
account active	admin	en	Account active
account list	admin	en	Account list
account permissions	admin	en	Account permissions
account preferences	admin	en	Account Preferences
acl manager	admin	en	ACL Manager
acl rights	admin	en	ACL Rights
action	admin	en	Action
add a category	admin	en	add a category
add a group	admin	en	add a group
add a new account.	admin	en	Add a new account.
add a subcategory	admin	en	add a subcategory
add a user	admin	en	add a user
add account	admin	en	Add account
add application	admin	en	Add application
add auto-created users to this group ('default' will be attempted if this is empty.)	admin	en	Add auto-created users to this group ('Default' will be attempted if this is empty.)
add global category	admin	en	Add global category
add global category for %1	admin	en	Add global category for %1
add group	admin	en	Add group
add new account	admin	en	Add new account
add new application	admin	en	Add new application
add peer server	admin	en	Add Peer Server
add sub-category	admin	en	Add sub-category
admin email	admin	en	Admin Email
admin email addresses (comma-separated) to be notified about the blocking (empty for no notify)	admin	en	Admin email addresses (comma-separated) to be notified about the blocking (empty for no notify)
admin name	admin	en	Admin Name
administration	admin	en	Administration
admins	admin	en	Admins
after how many unsuccessful attempts to login, an account should be blocked (default 3) ?	admin	en	After how many unsuccessful attempts to login, an account should be blocked (default 3) ?
after how many unsuccessful attempts to login, an ip should be blocked (default 3) ?	admin	en	After how many unsuccessful attempts to login, an IP should be blocked (default 3) ?
all records and account information will be lost!	admin	en	All records and account information will be lost!
all users	admin	en	All Users
allow anonymous access to this app	admin	en	Allow anonymous access to this app
anonymous user	admin	en	Anonymous user
anonymous user (not shown in list sessions)	admin	en	Anonymous User (not shown in list sessions)
appearance	admin	en	Appearance
application	admin	en	Application
application name	admin	en	Application name
application title	admin	en	Application title
applications	admin	en	Applications
apply	admin	en	apply
applications list	admin	en	Applications list
are you sure you want to delete the application %1 ?	admin	en	Are you sure you want to delete the application %1 ?
are you sure you want to delete this account ?	admin	en	Are you sure you want to delete this account ?
are you sure you want to delete this application ?	admin	en	Are you sure you want to delete this application ?
are you sure you want to delete this global category ?	admin	en	Are you sure you want to delete this global category ?
are you sure you want to delete this group ?	admin	en	Are you sure you want to delete this group ?
are you sure you want to delete this server?	admin	en	Are you sure you want to delete this server?
are you sure you want to kill this session ?	admin	en	Are you sure you want to kill this session ?
async services last executed	admin	en	Async services last executed
asynchronous timed services	admin	en	Asynchronous timed services
asyncservices not yet installed or other error (%1) !!!	admin	en	asyncservices not yet installed or other error (%1) !!!
attempt to use correct mimetype for ftp instead of default 'application/octet-stream'	admin	en	Attempt to use correct mimetype for FTP instead of default 'application/octet-stream'
authentication / accounts	admin	en	Authentication / Accounts
auto create account records for authenticated users	admin	en	Auto create account records for authenticated users
back to the list	admin	en	back to the list
bi-dir passthrough	admin	en	bi-dir passthrough
bi-directional	admin	en	bi-directional
bottom	admin	en	bottom
category %1 has been saved !	admin	en	Category %1 has been saved !
calculate next run	admin	en	Calculate next run
can change password	admin	en	Can change password
cancel testjob!	admin	en	Cancel TestJob!
categories list	admin	en	Categories list
category list	admin	en	Category list
change acl rights	admin	en	change ACL Rights
change config settings	admin	en	Change config settings
change main screen message	admin	en	Change main screen message
check ip address of all sessions	admin	en	check ip address of all sessions
check items to <b>%1</b> to %2 for %3	admin	en	Check items to <b>%1</b> to %2 for %3
country selection	admin	en	Country Selection
create group	admin	en	Create Group
crontab only (recomended)	admin	en	crontab only (recomended)
data	admin	en	Data
day	admin	en	Day
day of week<br>(0-6, 0=sun)	admin	en	Day of week<br>(0-6, 0=Sun)
default	admin	en	Default
default file system space per user	admin	en	Default file system space per user
default file system space per user/group ?	admin	en	Default file system space per user/group ?
delete account	admin	en	Delete account
delete all records	admin	en	Delete All Records
delete the category	admin	en	delete the category
delete the group	admin	en	delete the group
delete this category	admin	en	delete this category
delete this group	admin	en	delete this group
delete this user	admin	en	delete this user
delete application	admin	en	Delete application
delete category	admin	en	Delete category
delete group	admin	en	Delete group
delete peer server	admin	en	Delete peer server
deny access to access log	admin	en	Deny access to access log
deny access to application registery	admin	en	Deny access to application registery
deny access to applications	admin	en	Deny access to applications
deny access to asynchronous timed services	admin	en	Deny access to asynchronous timed services
deny access to current sessions	admin	en	Deny access to current sessions
deny access to error log	admin	en	Deny access to error log
deny access to global categories	admin	en	Deny access to global categories
deny access to groups	admin	en	Deny access to groups
deny access to mainscreen message	admin	en	Deny access to mainscreen message
deny access to peer servers	admin	en	Deny access to peer servers
deny access to phpinfo	admin	en	Deny access to phpinfo
deny access to site configuration	admin	en	Deny access to site configuration
deny access to user accounts	admin	en	Deny access to user accounts
deny all users access to grant other users access to their entries ?	admin	en	Deny all users access to grant other users access to their entries ?
description can not exceed 255 characters in length !	admin	en	Description can not exceed 255 characters in length !
disable "auto completion" of the login form	admin	en	Disable "auto completion" of the login form
disabled (not recomended)	admin	en	disabled (not recomended)
display	admin	en	Display
do not delete the category and return back to the list	admin	en	do NOT delete the category and return back to the list
do you also want to delete all global subcategories ?	admin	en	Do you also want to delete all global subcategories ?
do you want to delete all global subcategories ?	admin	en	Do you want to delete all global subcategories ?
do you want to move all global subcategories one level down ?	admin	en	Do you want to move all global subcategories one level down ?
edit account	admin	en	Edit account
edit application	admin	en	Edit application
edit global category	admin	en	Edit global category
edit global category for %1	admin	en	Edit global category for %1
edit group	admin	en	Edit Group
edit login screen message	admin	en	Edit login screen message
edit main screen message	admin	en	Edit main screen message
edit peer server	admin	en	Edit Peer Server
edit table format	admin	en	Edit Table format
edit this category	admin	en	edit this category
edit this group	admin	en	edit this group
edit this user	admin	en	edit this user
edit user	admin	en	edit user
edit user account	admin	en	Edit user account
enable debug-messages	admin	en	Enable debug-messages
enabled - hidden from navbar	admin	en	Enabled - Hidden from navbar
enter a description for the category	admin	en	enter a description for the category
enter some random text for app_session <br>encryption (requires mcrypt)	admin	en	Enter some random text for app_session <br>encryption (requires mcrypt)
enter the background color for the login page	admin	en	Enter the background color for the login page
enter the background color for the site title	admin	en	Enter the background color for the site title
enter the file name of your login logo	admin	en	Enter the file name of your login logo
enter the file name of your logo	admin	en	Enter the file name of your logo
enter the full path for temporary files.<br>examples: /tmp, c:\\temp	admin	en	Enter the full path for temporary files.<br>Examples: /tmp, C:\\TEMP
enter the full path for users and group files.<br>examples: /files, e:\\files	admin	en	Enter the full path for users and group files.<br>Examples: /files, E:\\FILES
enter the hostname of the machine on which this server is running	admin	en	Enter the hostname of the machine on which this server is running
enter the location of phpgroupware's url.<br>example: http://www.domain.com/phpgroupware &nbsp; or &nbsp; /phpgroupware<br><b>no trailing slash</b>	admin	en	Enter the location of phpGroupWare's URL.<br>Example: http://www.domain.com/phpgroupware &nbsp; or &nbsp; /phpgroupware<br><b>No trailing slash</b>
enter the search string. to show all entries, empty this field and press the submit button again	admin	en	Enter the search string. To show all entries, empty this field and press the SUBMIT button again
enter the site password for peer servers	admin	en	Enter the site password for peer servers
enter the site username for peer servers	admin	en	Enter the site username for peer servers
enter the title for your site	admin	en	Enter the title for your site
enter the title of your logo	admin	en	Enter the title of your logo
enter the url where your logo should link to	admin	en	Enter the url where your logo should link to
enter your default ftp server	admin	en	Enter your default FTP server
enter your http proxy server	admin	en	Enter your HTTP proxy server
enter your http proxy server port	admin	en	Enter your HTTP proxy server port
error canceling timer, maybe there's none set !!!	admin	en	Error canceling timer, maybe there's none set !!!
error setting timer, wrong syntax or maybe there's one already running !!!	admin	en	Error setting timer, wrong syntax or maybe there's one already running !!!
error: %1 not found or other error !!!	admin	en	Error: %1 not found or other error !!!
expires	admin	en	Expires
fallback (after each pageview)	admin	en	fallback (after each pageview)
file space	admin	en	File space
file space must be an integer	admin	en	File space must be an integer
find and register all application hooks	admin	en	Find and Register all Application Hooks
for the times above	admin	en	for the times above
for the times below (empty values count as '*', all empty = every minute)	admin	en	for the times below (empty values count as '*', all empty = every minute)
force selectbox	admin	en	Force Selectbox
global categories	admin	en	Global Categories
group ?	admin	en	group ?
group list	admin	en	Group list
group manager	admin	en	Group Manager
group name	admin	en	Group Name
hide php information	admin	en	hide php information
home directory	admin	en	Home directory
host information	admin	en	Host information
hour<br>(0-23)	admin	en	Hour<br>(0-23)
how many days should entries stay in the access log, before they get deleted (default 90) ?	admin	en	How many days should entries stay in the access log, before they get deleted (default 90) ?
how many minutes should an account or ip be blocked (default 30) ?	admin	en	How many minutes should an account or IP be blocked (default 30) ?
idle	admin	en	idle
if no acl records for user or any group the user is a member of	admin	en	If no ACL records for user or any group the user is a member of
if using ldap, do you want to manage homedirectory and loginshell attributes?	admin	en	If using LDAP, do you want to manage homedirectory and loginshell attributes?
inbound	admin	en	inbound
install crontab	admin	en	Install crontab
installed crontab	admin	en	Installed crontab
interface	admin	en	Interface
ip	admin	en	IP
jobs	admin	en	Jobs
kill	admin	en	Kill
kill session	admin	en	Kill session
last %1 logins	admin	en	Last %1 logins
last %1 logins for %2	admin	en	Last %1 logins for %2
last login	admin	en	last login
last login from	admin	en	last login from
last time read	admin	en	Last Time Read
ldap accounts context	admin	en	LDAP accounts context
ldap default homedirectory prefix (e.g. /home for /home/username)	admin	en	LDAP Default homedirectory prefix (e.g. /home for /home/username)
ldap default shell (e.g. /bin/bash)	admin	en	LDAP Default shell (e.g. /bin/bash)
ldap encryption type	admin	en	LDAP encryption type
ldap groups context	admin	en	LDAP groups context
ldap host	admin	en	LDAP host
ldap root password	admin	en	LDAP root password
ldap rootdn	admin	en	LDAP rootdn
leave the category untouched and return back to the list	admin	en	leave the category untouched and return back to the list
leave the group untouched and return back to the list	admin	en	Leave the group untouched and return back to the list
list config settings	admin	en	List config settings
list current sessions	admin	en	List current sessions
list of current users	admin	en	list of current users
login history	admin	en	Login History
login message	admin	en	Login message
login screen	admin	en	Login screen
login shell	admin	en	Login shell
login time	admin	en	Login Time
loginid	admin	en	LoginID
main screen message	admin	en	Main screen message
manager	admin	en	Manager
maximum account id (e.g. 65535 or 1000000)	admin	en	Maximum account id (e.g. 65535 or 1000000)
maximum entries in click path history	admin	en	Maximum entries in click path history
message has been updated	admin	en	message has been updated
method	admin	en	Method
minimum account id (e.g. 500 or 100, etc.)	admin	en	Minimum account id (e.g. 500 or 100, etc.)
minute	admin	en	Minute
mode	admin	en	Mode
month	admin	en	Month
new group name	admin	en	New group name
new password [ leave blank for no change ]	admin	en	New password [ Leave blank for no change ]
next run	admin	en	Next run
no algorithms available	admin	en	no algorithms available
no jobs in the database !!!	admin	en	No jobs in the database !!!
no login history exists for this user	admin	en	No login history exists for this user
no matches found	admin	en	No matches found
no modes available	admin	en	no modes available
no permission to add groups	admin	en	no permission to add groups
no permission to add users	admin	en	no permission to add users
no permission to create groups	admin	en	no permission to create groups
note: ssl available only if php is compiled with curl support	admin	en	Note: SSL available only if PHP is compiled with curl support
outbound	admin	en	outbound
passthrough	admin	en	passthrough
path information	admin	en	Path information
peer server list	admin	en	Peer server list
peer servers	admin	en	Peer servers
percent of users that logged out	admin	en	Percent of users that logged out
percent this user has logged out	admin	en	Percent this user has logged out
permissions	admin	en	Permissions
permissions this group has	admin	en	Permissions this group has
phpinfo	admin	en	PHP information
please enter a name	admin	en	Please enter a name
please enter a name for that server !	admin	en	Please enter a name for that server !
please run setup to become current	admin	en	Please run setup to become current
please select	admin	en	Please Select
preferences	admin	en	Preferences
re-enter password	admin	en	Re-enter password
read this list of methods.	admin	en	Read this list of methods.
register application hooks	admin	en	Register application hooks
remove all users from this group	admin	en	Remove all users from this group
remove all users from this group ?	admin	en	Remove all users from this group ?
return to admin mainscreen	admin	en	return to admin mainscreen
return to view account	admin	en	Return to view account
save the category	admin	en	save the category
save the category and return back to the list	admin	en	save the category and return back to the list
run asynchronous services	admin	en	Run Asynchronous services
search accounts	admin	en	Search accounts
search categories	admin	en	Search categories
search groups	admin	en	Search groups
search peer servers	admin	en	Search peer servers
security	admin	en	Security
select group managers	admin	en	Select Group Managers
select permissions this group will have	admin	en	Select permissions this group will have
select the parent category. if this is a main category select no category	admin	en	Select the parent category. If this is a main category select NO CATEGORY
select users for inclusion	admin	en	Select users for inclusion
select where you want to store/retrieve filesystem information	admin	en	Select where you want to store/retrieve filesystem information
select where you want to store/retrieve user accounts	admin	en	Select where you want to store/retrieve user accounts
select which location this app should appear on the navbar, lowest (left) to highest (right)	admin	en	Select which location this app should appear on the navbar, lowest (left) to highest (right)
selectbox	admin	en	Selectbox
server %1 has been updated	admin	en	Server %1 has been updated
server list	admin	en	Server List
server password	admin	en	Server Password
server type(mode)	admin	en	Server Type(mode)
server url	admin	en	Server URL
server username	admin	en	Server Username
set preference values.	admin	en	Set preference values.
show 'powered by' logo on	admin	en	Show 'powered by' logo on
show access log	admin	en	Show access log
show current action	admin	en	Show current action
show error log	admin	en	Show error log
show phpinfo()	admin	en	Show phpinfo()
show session ip address	admin	en	Show session IP address
site	admin	en	Site
site configuration	admin	en	Site configuration
soap	admin	en	SOAP
sorry, that group name has already been taken.	admin	en	Sorry, that group name has already been taken.
sorry, the follow users are still a member of the group %1	admin	en	Sorry, the follow users are still a member of the group %1
sort the entries	admin	en	sort the entries
ssl	admin	en	ssl
standard	admin	en	standard
start testjob!	admin	en	Start TestJob!
submit changes	admin	en	Submit Changes
submit the search string	admin	en	Submit the search string
template selection	admin	en	Template Selection
text entry	admin	en	Text Entry
that application name already exists.	admin	en	That application name already exists.
that application order must be a number.	admin	en	That application order must be a number.
that loginid has already been taken	admin	en	That loginid has already been taken
that name has been used already	admin	en	That name has been used already
that server name has been used already !	admin	en	That server name has been used already !
the api is current	admin	en	The API is current
the api requires an upgrade	admin	en	The API requires an upgrade
the login and password can not be the same	admin	en	The login and password can not be the same
the loginid can not be more then 8 characters	admin	en	The loginid can not be more then 8 characters
the testjob sends you a mail everytime it is called.	admin	en	The TestJob sends you a mail everytime it is called.
the two passwords are not the same	admin	en	The two passwords are not the same
the users bellow are still members of group %1	admin	en	the users bellow are still members of group %1
they must be removed before you can continue	admin	en	They must be removed before you can continue
this application is current	admin	en	This application is current
this application requires an upgrade	admin	en	This application requires an upgrade
this category is currently being used by applications as a parent category	admin	en	This category is currently being used by applications as a parent category.
timeout for application session data in seconds (default 86400 = 1 day)	admin	en	Timeout for application session data in seconds (default 86400 = 1 day)
timeout for sessions in seconds (default 14400 = 4 hours)	admin	en	Timeout for sessions in seconds (default 14400 = 4 hours)
times	admin	en	Times
top	admin	en	top
total records	admin	en	Total records
trust level	admin	en	Trust Level
trust relationship	admin	en	Trust Relationship
under windows you can only use the fallback mode at the moment. fallback means the jobs get only checked after each page-view !!!	admin	en	Under windows you can only use the fallback mode at the moment. Fallback means the jobs get only checked after each page-view !!!
use cookies to pass sessionid	admin	en	Use cookies to pass sessionid
use pure html compliant code (not fully working yet)	admin	en	Use pure HTML compliant code (not fully working yet)
use theme	admin	en	Use theme
user accounts	admin	en	User accounts
user data	admin	en	User Data
user groups	admin	en	User groups
userdata	admin	en	userdata
users choice	admin	en	Users Choice
view access log	admin	en	View access log
view account	admin	en	View account
view category	admin	en	View category
view error log	admin	en	View error log
view sessions	admin	en	View sessions
view this user	admin	en	view this user
view user account	admin	en	View user account
who would you like to transfer all records owned by the deleted user to?	admin	en	Who would you like to transfer ALL records owned by the deleted user to?
would you like phpgroupware to cache the phpgw info array ?	admin	en	Would you like phpGroupWare to cache the phpgw info array ?
would you like phpgroupware to check for new application versions when admins login ?	admin	en	Would you like phpGroupWare to check for new application versions when admins login ?
would you like to automaticaly load new langfiles (at login-time) ?	admin	en	Would you like to automaticaly load new langfiles (at login-time) ?
would you like to show each application's upgrade status ?	admin	en	Would you like to show each application's upgrade status ?
xml-rpc	admin	en	XML-RPC
you have entered an invalid expiration date	admin	en	You have entered an invalid expiration date
you must add at least 1 permission or group to this account	admin	en	You must add at least 1 permission or group to this account
you must enter a group name.	admin	en	You must enter a group name.
you must enter a loginid	admin	en	You must enter a loginid
you must enter an application name and title.	admin	en	You must enter an application name and title.
you must enter an application name.	admin	en	You must enter an application name.
you must enter an application title.	admin	en	You must enter an application title.
you must select a file type	admin	en	You must select a file type
you will need to remove the subcategories before you can delete this category	admin	en	You will need to remove the subcategories before you can delete this category !
change your password	preferences	no	Endre passord
change your settings	preferences	no	Endre innstillinger
date format	preferences	no	Dato format
email signature	preferences	no	E-Post signatur
enter your new password	preferences	no	Skriv inn ditt nye passord
language	preferences	no	Språk
use default	preferences	no	Bruk standard
max matches per page	preferences	no	Antall treff per side
any listing in phpgw will show you this number of entries or lines per page.<br>to many slow down the page display, to less will cost you the overview.	preferences	no	Alle lister vil vise dette antall treff per side.<br/>For mange vil gå utover innlastingstiden, for få vil hindre oversikt
interface/template selection	preferences	no	Template-sett
a template defines the layout of phpgroupware and it contains icons for each application.	preferences	no	En template definerer utseende for BkBygg og inneholder ikoner for alle moduler.
default	preferences	no	Standard
theme (colors/fonts) selection	preferences	no	Tema (farger/fonter)
a theme defines the colors and fonts used by the template.	preferences	no	Et tema definerer farger og fonter som brukes at templaten.
note: this feature does *not* change your email password. this will need to be done manually.	preferences	no	NB: Denne funksjonen endrer *ikke* ditt epost passord. Dette må gjøres manuelt.
please, select a new theme	preferences	no	Vennligst velg et nytt tema
re-enter your password	preferences	no	Skriv inn ditt passord igjen
select different theme	preferences	no	Velg annet tema
show birthday reminders on main screen	preferences	no	Vis fødselsdags påminnere på hovedskjerm
show current users on navigation bar	preferences	no	Vis current brukere i navigation bar
show high priority events on main screen	preferences	no	Vis høyprioritets events på hovedskjermen
show new messages on main screen	preferences	no	Vis nye meldinger på hovedskjerm
show text on navigation icons	preferences	no	Vis tekst på navigasjons ikoner
the two passwords are not the same	preferences	no	Passordene stemmer ikke overens
this server is located in the %1 timezone	preferences	no	tids-sonen
time format	preferences	no	Tids format
time zone offset	preferences	no	Tids-sone offset
weekday starts on	preferences	no	Ukedag begynner på
work day ends on	preferences	no	Arbeidsdag slutter på
work day starts on	preferences	no	Arbeidsdag begynner på
you must enter a password	preferences	no	Du må skrive inn et passord
your current theme is: %1	preferences	no	</b>
your preferences	preferences	no	Dine innstillinger
default preferences	preferences	no	Standardinnstillinger
forced preferences	preferences	no	Påkrevde innstillinger
time zone	preferences	no	Tidssone
how should phpgroupware display dates for you.	preferences	no	Hvordan datoer vises for deg
do you prefer a 24 hour time format, or a 12 hour one with am/pm attached.	preferences	no	Foretrekker du 24-timersformat eller 12-timersformat med am/pm?
country	preferences	no	Land
in which country are you. this is used to set certain defaults for you.	preferences	no	Hvilket land er du i? Denne brukes for å sette standardverdier basert på landet du er i
show number of current users	preferences	no	Vis antall innloggede brukere
should the number of active sessions be displayed for you all the time.	preferences	no	Skal antall aktive brukere vises?
currency	preferences	no	Valuta
which currency symbol or name should be used in phpgroupware.	preferences	no	Hvilket valuta-symbol eller navn skal brukes i BkBygg
how do you like to select accounts	preferences	no	Hvordan vil du velge konto?
selectbox	preferences	no	Nedtrekksliste
popup with search	preferences	no	Pop-up vindu med søk
the selectbox shows all available users (can be very slow on big installs with many users). the popup can search users by name or group.	preferences	no	Nedtrekkslisten viser alle tilgjengelige brukere (kan være treg på installasjoner med mange brukere). Pop-up vinduet kan søke på navn eller gruppe
how do you like to display accounts	preferences	no	Hvordan vil du vise kontoer?
firstname	preferences	no	Fornavn
lastname	preferences	no	Etternavn
12 hour	preferences	no	12-timer
24 hour	preferences	no	24-timer
rich text (wysiwyg) editor	preferences	no	Rik tekst (WISYWIG) editor
which editor would you like to use for editing html and other rich content?	preferences	no	Hvilken editor skal brukes for å redigere HTML og annet rikt tekst-innhold?
show helpmessages by default	preferences	no	Vis hjelpemeldinger som standard
should this help messages shown up always, when you enter the preferences or only on request.	preferences	no	Skal hjelpemeldinger alltid vises, når du skriver inn innstillingene eller på forepørsel?
sidecontent	preferences	no	Sideinnhold
do you want your menues as sidecontent	preferences	no	Vil du ha menyer vist som sideinnhold?
show breadcrumbs	preferences	no	Vis brødsmulesti
select user	preferences	no	Velg bruker
default application	preferences	no	Standardmodul
the default application will be started when you enter phpgroupware or click on the homepage icon.<br>you can also have more than one application showing up on the homepage, if you don't choose a specific application here (has to 	preferences	no	Standardmodul som vises når du logger inn i BkBygg eller klikker på Hjemme-lenken.
a time zone is a region of the earth that has uniform standard time, usually referred to as the local time. by convention, time zones compute their local time as an offset from utc	preferences	no	En tidssone er en region som har en uniform standardtid, som oftest referert til som lokal tid. Tidssoner beregner sin lokale tid basert på avstand fra UTC
set this to your convenience. for security reasons, you might not want to show your loginname in public.	preferences	no	Sett denne til det du ønsker. Av sikkerhetsmessige hensyn bør ikke påloggingsnavnet vises offentlig.
should history navigation urls as breadcrumbs	preferences	no	Vis historisk navigasjon som brødsmulesti
activate nowrap in yui-tables	preferences	no	Aktiver no-wrap i YUI-tabeller
select the language of texts and messages within phpgroupware.<br>some languages may not contain all messages, in that case you will see an english message.	preferences	no	Velg språk for tekster og meldinger i BkBygg.<br/>Dersom en oversettelse mangler vil engelsk tekst/melding vises
users choice	preferences	no	Brukers valg
norwegian	preferences	no	Norsk Bokmål
english	preferences	no	Engelsk
no default	preferences	no	Ingen standard
no sidecontent	preferences	no	Ingen sideinnhold
choose property filter	preferences	no	Velg eiendomsfilter
filter by owner or owner type	preferences	no	Filtrer på eier eller eiertype
group filters in single query	preferences	no	Grupper filtre i en spørring
group filters - means that one has to hit the search button to apply the filter	preferences	no	Grupper filtre betyr at en må klikke på Søk-knappen for å aktivere filtreringsvalg
show new/updated tickets on main screen	preferences	no	Vis nye/oppdaterte meldinger på hjemmeskjermen
link to tickets you are assigned to	preferences	no	Lenke til meldinger tildelt deg
default ticket status	preferences	no	Standard meldingsstatus
the default status when entering the helpdesk and mainscreen	preferences	no	Standard meldingsstatus når en går inn på meldinger
custom title on main screen tickets	preferences	no	Egendefinert tittel på meldinger på hjemmeskjermen
show updated tickets on main screen 2	preferences	no	Vis oppdaterte meldinger på hjemmeskjerm 2
default ticket status 2	preferences	no	Standard meldingsstatus 2
show updated tickets on main screen 3	preferences	no	Vis oppdaterte meldinger på hjemmeskjerm 3
default ticket status 3	preferences	no	Standard meldingsstatus 3
show updated tickets on main screen 4	preferences	no	Vis oppdaterte meldinger på hjemmeskjerm 4
default ticket status 4	preferences	no	Standard meldingsstatus 4
show pending vendor reminders on main screen	preferences	no	Vis ventende leverandør-purringer på hjemmeskjerm
reminder issued to vendors	preferences	no	Purringer sendt til leverandører
custom title on pending vendor reminders	preferences	no	Egendefinert tittel på leverandørpurringer
show your pending request for approvals on main screen	preferences	no	Vis ventende godkjenningsforespørsler på hjemmeskjerm
your requests for approvals waiting decisions	preferences	no	Dine meldinger som avventer godkjenning
custom title on pending request for approvals	preferences	no	Egendefinert tittel på ventende godkjenningsforespørsler
show pending approvals on main screen	preferences	no	Vis meldinger du må godkjenne på hjemmeskjerm
approvals waiting for your decisions	preferences	no	Meldinger sendt til deg for godkjenning
custom title on pending approvals	preferences	no	Egendefinert tittel på godkjenningsforespørsler
default updated ticket status when creating project	preferences	no	Standard oppdatert meldingsstatus ved oppretting av prosjekt
autocreate project from ticket	preferences	no	Opprett prosjekt fra melding
your projects on main screen - list 1	preferences	no	Vis dine prosjekter på hjemmeskjerm - liste 1
link to your projects	preferences	no	Lenke til dine prosjekter
default project status 1	preferences	no	Standard prosjektstatus 1
the default status for list 1 when entering the mainscreen	preferences	no	Standard status for liste 1 når du går inn på hjemmeskjerm
the default status for list 2 when entering the mainscreen	preferences	no	Standard status for liste 2 når du går inn på hjemmeskjerm
custom title on projects on main screen - list 1	preferences	no	Egendefinert tittel på prosjekter på hjemmeskjerm - liste 1
your workorders on main screen - list 1	preferences	no	Dine arbeidsordre på hjemmeskjerm - liste 1
link to your workorders	preferences	no	Lenke til dine arbeidsordre
default workorder status 1	preferences	no	Standard arbeidsordrestatus 1
custom title on workorders on main screen - list 1	preferences	no	Egendefinert tittel på arbeidsordre på hjemmeskjerm - liste 1
your workorders on main screen - list 2	preferences	no	Dine arbeidsordre på hjemmeskjerm - liste 2
default workorder status 2	preferences	no	Standard arbeidsordrestatus 2
custom title on workorders on main screen - list 2	preferences	no	Egendefinert tittel på arbeidsordre på hjemmeskjerm - liste 2
custom title workorders on main screen - list 2	preferences	no	Egendefinert tittel på arbeidsordre på hjemmeskjerm - liste 2
show quick link for changing status for tickets	preferences	no	Vis hurtiglenke for å endre meldingsstatus
enables to set status wihout entering the ticket	preferences	no	Muliggjør å sette meldingsstatus uten å åpne meldingen
default group tts	preferences	no	Standardgruppe tts
the default group to assign a ticket in helpdesk-submodul	preferences	no	Standardgruppe for tildeling av meldinger i helpdesk
default assign to tts	preferences	no	Standardbruker tts
the default user to assign a ticket in helpdesk-submodule	preferences	no	Standardbruker for tildeling av meldinger i helpdesk
default priority tts	preferences	no	Standardprioritet tts
the default priority for tickets in the helpdesk-submodule	preferences	no	Standardprioritet for meldinger i helpdesk
default ticket categories	preferences	no	Standard meldingskategori
the default category for tts	preferences	no	Standard meldingskategori i tts
notify me by mail when ticket is assigned or altered	preferences	no	Varsle meg på e-post når meldinger er tildelt eller endret
send e-mail from tts	preferences	no	Send e-post fra tts
send e-mail from tts as default	preferences	no	Sendt e-post fra tts som standard
refresh tts every (seconds)	preferences	no	Oppfrisk tts hvert (sekunder)
the intervall for helpdesk refresh - cheking for new tickets	preferences	no	Intervall for oppfrisking av helpdesk - sjekk om det er nye meldinger
set myself as contact when adding a ticket	preferences	no	Sett meg selv som kontaktperson ved oppretting av nye meldinger
default degree request safety	preferences	no	Standard grad av alvorlighet
the degree of seriousness	preferences	no	Grad av alvorlighet
default degree request aesthetics	preferences	no	Standard estetikk-grad på behov
default degree request indoor climate	preferences	no	Standard inneklima-grad på behov
default degree request consequential damage	preferences	no	Standard konsekvens-grad på behov
default degree request user gratification	preferences	no	Standard bruker-grad på behov
default degree request residential environment	preferences	no	Standard miljø-grad på behov
send order receipt as email	preferences	no	Send ordrebekreftelse som e-post
send the order as bcc to the user	preferences	no	Send blindkopi av ordre til bruker
notify owner of project/order on change	preferences	no	Varsle prosjekt-/ordre-eier ved endring
by email	preferences	no	Varsle prosjekt- eller ordre-eier per e-post ved endringer
default start page	preferences	no	Standard startside
select your start-submodule	preferences	no	Velg undermodul du vil starte i når du går inn på modulen
default project type	preferences	no	Standard prosjekttype
select your default project type	preferences	no	Velg standard prosjekttype for dine prosjekter
default project year filter	preferences	no	Standard år-filter for prosjekt
select your default project year filter	preferences	no	Velg standard år-filter for prosjekter
default project status	preferences	no	Stardard prosjektstatus
the default status for your projects	preferences	no	Velg standard prosjektstatus for dine prosjekter
default workorder status	preferences	no	Standard arbeidsordre-status
the default status for your workorders	preferences	no	Velg standard status for dine arbeidsordre
default project categories	preferences	no	Standard prosjektkategori
the default category for your projects and workorders	preferences	no	Velg standard kategori for dine prosjekter og arbeidsordre
default district-filter	preferences	no	Standard distrikt-filter
your default district-filter	preferences	no	Velg standard distrikt-filter
your cellphone	preferences	no	Ditt mobiltelefonnummer
ressursnr	preferences	no	Ditt ressursnummer
default dimb	preferences	no	Standard ansvarssted
your email	preferences	no	Din e-postadresse
insert your email address	preferences	no	Legg inn din e-postadresse
branch tts	preferences	no	Vis fag for melding
enable branch in tts-orders	preferences	no	Vis fag for melding-bestilling
default vendor type	preferences	no	Standard leverandørtype
which agreement	preferences	no	Velg leverandørtype du vil ha som standard
with of textarea	preferences	no	Bredde på tekstbokser
with of textarea in forms	preferences	no	Legg inn ønsket bredde på tekstbokser i skjema
height of textarea	preferences	no	Høyde på tekstbokser
height of textarea in forms	preferences	no	Legg inn ønsket høyde på tekstbokser i skjema
show horisontal menues	preferences	no	Vis horisontale menyer
horisontal menues are shown in top of page	preferences	no	Horisontale menyer vises i toppen av siden
remove navbar	preferences	no	Fjern navigasjonslinje
navigation bar is removed	preferences	no	Skal navigasjonslinje vises eller ikke
tabel export format	preferences	no	Eksportformat for lister og tabeller
choose which format to export from the system for tables	preferences	no	Velg hvilket format som skal brukes ved eksport av tabeller og lister
order email	preferences	no	E-postformat for ordre
current year	preferences	no	Gjeldende år
workorder approval from	preferences	no	Godkjenning av arbeidsordre gis av
if you need approval from your supervisor for projects/workorders	preferences	no	Dersom du trenger godkjenning av leder for prosjekter eller arbeidsordre angis denne her
eiendom - preferences	preferences	no	Eiendom - innstillinger
0 - 2	preferences	no	0 - 2
as in "." or ","	preferences	no	Velg "." eller ","
number of planned controls on home page	preferences	no	Antall planlagte kontroller på hjemmeskjerm
number of assigned controls on home page	preferences	no	Antall tildelte kontroller på hjemmeskjerm
filter tickets on assigned to me	preferences	no	Filtrer meldinger på tildelt til meg.
do you want av csv download button for main tables?	preferences	no	Ønsker du en CSV nedlastingsknapp for hovedtabeller?
csv download button	preferences	no	CSV nedlastingsknapp
help off	preferences	no	Slå av hjelp
%1 - preferences	preferences	en	%1 - Preferences
%1 hours	preferences	en	%1 hours
12 hour	preferences	en	12 hour
24 hour	preferences	en	24 hour
a template defines the layout of phpgroupware and it contains icons for each application.	preferences	en	A template defines the layout of phpGroupWare and it contains icons for each application.
a theme defines the colors and fonts used by the template.	preferences	en	A theme defines the colors and fonts used by the template.
acl grants have been updated	preferences	en	ACL grants have been updated
any listing in phpgw will show you this number of entries or lines per page.<br>to many slow down the page display, to less will cost you the overview.	preferences	en	Any listing in phpGW will show you this number of entries or lines per page.<br>To many slow down the page display, to less will cost you the overview.
are you sure you want to delete this category ?	preferences	en	Are you sure you want to delete this category ?
change your password	preferences	en	Change your Password
change your profile	preferences	en	Change your profile
change your settings	preferences	en	Change your Settings
country	preferences	en	Country
date format	preferences	en	Date format
default	preferences	en	default
default application	preferences	en	Default application
default preferences	preferences	en	Default Preferences
delete categories	preferences	en	Delete Categories
description can not exceed 255 characters in length !	preferences	en	Description can not exceed 255 characters in length !
do you prefer a 24 hour time format, or a 12 hour one with am/pm attached.	preferences	en	Do you prefer a 24 hour time format, or a 12 hour one with am/pm attached.
edit custom fields	preferences	en	edit custom fields
enter your new password	preferences	en	Enter your new password
error: there was a problem finding the preference file for %1 in %2	preferences	en	Error: There was a problem finding the preference file for %1 in %2
forced preferences	preferences	en	Forced Preferences
help off	preferences	en	Help off
hours	preferences	en	hours
how do you like to display accounts	preferences	en	How do you like to display accounts
how do you like to select accounts	preferences	en	How do you like to select accounts
how many hours are you in front or after the timezone of the server.<br>if you are in the same time zone as the server select 0 hours, else select your locale date and time.	preferences	en	How many hours are you in front or after the timezone of the server.<br>If you are in the same time zone as the server select 0 hours, else select your locale date and time.
how should phpgroupware display dates for you.	preferences	en	How should phpGroupWare display dates for you.
icons and text	preferences	en	Icons and text
icons only	preferences	en	icons only
in which country are you. this is used to set certain defaults for you.	preferences	en	In which country are you. This is used to set certain defaults for you.
interface/template selection	preferences	en	Interface/Template Selection
language	preferences	en	Language
max matches per page	preferences	en	Max matches per page
no default	preferences	en	No default
note: this feature does *not* change your email password. this will need to be done manually.	preferences	en	Note: This feature does *not* change your email password. This will need to be done manually.
please, select a new theme	preferences	en	Please, select a new theme
popup with search	preferences	en	Popup with search
re-enter your password	preferences	en	Re-Enter your password
select different theme	preferences	en	Select different Theme
select one	preferences	en	Select one
select the language of texts and messages within phpgroupware.<br>some languages may not contain all messages, in that case you will see an english message.	preferences	en	Select the language of texts and messages within phpGroupWare.<br>Some languages may not contain all messages, in that case you will see an english message.
selectbox	preferences	en	Selectbox
set this to your convenience. for security reasons, you might not want to show your loginname in public.	preferences	en	Set this to your convenience. For security reasons, you might not want to show your Loginname in public.
should the number of active sessions be displayed for you all the time.	preferences	en	Should the number of active sessions be displayed for you all the time.
should this help messages shown up always, when you enter the preferences or only on request.	preferences	en	Should this help messages shown up always, when you enter the preferences or only on request.
show helpmessages by default	preferences	en	Show helpmessages by default
show navigation bar as	preferences	en	Show navigation bar as
show number of current users	preferences	en	Show number of current users
show text on navigation icons	preferences	en	Show text on navigation icons
text only	preferences	en	Text only
the default application will be started when you enter phpgroupware or click on the homepage icon.<br>you can also have more than one application showing up on the homepage, if you don't choose a specific application here (has to 	preferences	en	The default application will be started when you enter phpGroupWare or click on the homepage icon.<br>You can also have more than one application showing up on the homepage, if you don't choose a specific application here (has to be configured in the preferences of each application).
the selectbox shows all available users (can be very slow on big installs with many users). the popup can search users by name or group.	preferences	en	The selectbox shows all available users (can be very slow on big installs with many users). The popup can search users by name or group.
the two passwords are not the same	preferences	en	The two passwords are not the same
theme (colors/fonts) selection	preferences	en	Theme (colors/fonts) Selection
this server is located in the %1 timezone	preferences	en	This server is located in the %1 timezone
time format	preferences	en	Time format
use default	preferences	en	Use default
users choice	preferences	en	Users choice
which currency symbol or name should be used in phpgroupware.	preferences	en	Which currency symbol or name should be used in phpGroupWare.
you can show the applications as icons only, icons with app-name or both.	preferences	en	You can show the applications as icons only, icons with app-name or both.
you do not have permission to set acl's in this mode!	preferences	en	You do not have permission to set ACL's in this mode!
you must enter a password	preferences	en	You must enter a password
your current theme is: %1	preferences	en	your current theme is: %1
your preferences	preferences	en	Your Preferences
\.


--
-- Data for Name: phpgw_languages; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_languages (lang_id, lang_name, available) FROM stdin;
aa	Afar	No 
ab	Abkhazian	No 
af	Afrikaans	No 
am	Amharic	No 
ar	Arabic	No 
as	Assamese	No 
ay	Aymara	No 
az	Azerbaijani	No 
ba	Bashkir	No 
be	Byelorussian	No 
bg	Bulgarian	No 
bh	Bihari	No 
bi	Bislama	No 
bn	Bengali	No 
bo	Tibetan	No 
br	Breton	No 
ca	Catalan	No 
co	Corsican	No 
cs	Czech	Yes
cy	Welsh	No 
da	Danish	Yes
de	German	Yes
dz	Bhutani	No 
el	Greek	No 
en	English	Yes
eo	Esperanto	No 
es	Spanish	Yes
et	Estonian	No 
eu	Basque	No 
fa	Persian	No 
fi	Finnish	No 
fj	Fiji	No 
fo	Faeroese	No 
fr	French	Yes
fy	Frisian	No 
ga	Irish	No 
gd	Scots Gaelic	No 
gl	Galician	No 
gn	Guarani	No 
gu	Gujarati	No 
ha	Hausa	No 
he	Hebrew	No 
hi	Hindi	No 
hr	Croatian	No 
hu	Hungarian	Yes
hy	Armenian	No 
ia	Interlingua	No 
ie	Interlingue	No 
ik	Inupiak	No 
id	Indonesian	No 
is	Icelandic	No 
it	Italian	Yes
iu	Inuktitut	No 
ja	Japanese	Yes
jw	Javanese	No 
ka	Georgian	No 
kk	Kazakh	No 
kl	Greenlandic	No 
km	Cambodian	No 
kn	Kannada	No 
ko	Korean	Yes
ks	Kashmiri	No 
ku	Kurdish	No 
ky	Kirghiz	No 
la	Latin	No 
ln	Lingala	No 
lo	Laothian	No 
lt	Lithuanian	No 
lv	Latvian / Lettish	No 
mg	Malagasy	No 
mi	Maori	No 
mk	Macedonian	No 
ml	Malayalam	No 
mn	Mongolian	No 
mo	Moldavian	No 
mr	Marathi	No 
ms	Malay	No 
mt	Maltese	No 
my	Burmese	No 
na	Nauru	No 
ne	Nepali	No 
nl	Dutch	Yes
no	Norwegian	Yes
oc	Occitan	No 
om	Oromo / Afan	No 
or	Oriya	No 
pa	Punjabi	No 
pl	Polish	Yes
ps	Pashto / Pushto	No 
pt	Portuguese	No 
qu	Quechua	No 
rm	Rhaeto-Romance	No 
rn	Kirundi	No 
ro	Romanian	No 
ru	Russian	No 
rw	Kinyarwanda	No 
sa	Sanskrit	No 
sd	Sindhi	No 
sg	Sangro	No 
sh	Serbo-Croatian	No 
si	Singhalese	No 
sk	Slovak	No 
sl	Slovenian	No 
sm	Samoan	No 
sn	Shona	No 
so	Somali	No 
sq	Albanian	No 
sr	Serbian	No 
ss	Siswati	No 
st	Sesotho	No 
su	Sudanese	No 
sv	Swedish	Yes
sw	Swahili	No 
ta	Tamil	No 
te	Tegulu	No 
tg	Tajik	No 
th	Thai	No 
ti	Tigrinya	No 
tk	Turkmen	No 
tl	Tagalog	No 
tn	Setswana	No 
to	Tonga	No 
tr	Turkish	Yes
ts	Tsonga	No 
tt	Tatar	No 
tw	Twi	No 
ug	Uigur	No 
uk	Ukrainian	No 
ur	Urdu	No 
uz	Uzbek	No 
vi	Vietnamese	No 
vo	Volapuk	No 
wo	Wolof	No 
xh	Xhosa	No 
yi	Yiddish	No 
yo	Yoruba	No 
zh	Chinese (Simplified)	No 
zt	Chinese (Traditional)	Yes
zu	Zulu	No 
nn	Norwegian NN	Yes
\.


--
-- Data for Name: phpgw_locations; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_locations (location_id, app_id, name, descr, allow_grant, allow_c_attrib, c_attrib_table, allow_c_function) FROM stdin;
1	1	run	Automatically added on install - run phpgwapi	0	\N	\N	0
2	1	admin	Allow app admins - phpgwapi	0	\N	\N	0
3	2	run	Automatically added on install - run admin	0	\N	\N	0
4	2	admin	Allow app admins - admin	0	\N	\N	0
5	3	run	Automatically added on install - run preferences	0	\N	\N	0
6	3	admin	Allow app admins - preferences	0	\N	\N	0
7	3	changepassword	allow user to change password	0	\N	\N	0
8	1	anonymous	allow anonymous sessions for public modules	0	\N	\N	0
9	2	vfs_filedata	config section for VFS filedata - file backend	0	\N	\N	0
10	4	run	Automatically added on install - run property	0	\N	\N	0
11	4	admin	Allow app admins - property	0	\N	\N	0
12	4	.	Top	1	\N	\N	\N
13	4	.admin	Admin	\N	\N	\N	\N
14	4	.admin.entity	Admin entity	\N	\N	\N	\N
15	4	.admin.location	Admin location	\N	\N	\N	\N
16	4	.location	Location	\N	\N	\N	\N
17	4	.location.1	Property	\N	\N	\N	\N
18	4	.location.2	Building	\N	\N	\N	\N
19	4	.location.3	Entrance	\N	\N	\N	\N
20	4	.location.4	Apartment	\N	\N	\N	\N
21	4	.custom	custom queries	\N	\N	\N	\N
22	4	.project	Demand -> Workorder	1	1	fm_project	1
23	4	.project.workorder	Workorder	1	1	fm_workorder	1
24	4	.project.workorder.transfer	Transfer Workorder	\N	\N	\N	1
25	4	.project.request	Request	1	1	fm_request	1
26	4	.ticket	Helpdesk	1	1	fm_tts_tickets	1
27	4	.ticket.order	Helpdesk ad hock order	\N	1	fm_tts_tickets	\N
28	4	.ticket.external	Helpdesk External user	\N	\N	\N	\N
29	4	.invoice	Invoice	\N	\N	\N	\N
30	4	.document	Documents	\N	\N	\N	\N
31	4	.drawing	Drawing	\N	\N	\N	\N
32	4	.b_account	Budget account	\N	\N	\N	\N
33	4	.tenant_claim	Tenant claim	\N	\N	\N	\N
34	4	.budget	Budet	\N	\N	\N	\N
35	4	.budget.obligations	Obligations	\N	\N	\N	\N
36	4	.budget.basis	Basis for high level lazy budgeting	\N	\N	\N	\N
37	4	.ifc	ifc integration	\N	\N	\N	\N
38	4	.agreement	Agreement	\N	1	fm_agreement	\N
39	4	.s_agreement	Service agreement	\N	1	fm_s_agreement	\N
40	4	.s_agreement.detail	Service agreement detail	\N	1	fm_s_agreement_detail	\N
41	4	.r_agreement	Rental agreement	\N	1	fm_r_agreement	\N
42	4	.r_agreement.detail	Rental agreement detail	\N	1	fm_r_agreement_detail	\N
43	4	.tenant	Tenant	1	1	fm_tenant	\N
44	4	.owner	Owner	1	1	fm_owner	\N
45	4	.vendor	Vendor	1	1	fm_vendor	\N
46	4	.jasper	JasperReport	1	\N	\N	0
47	4	.invoice.dimb	A dimension for accounting	1	\N	\N	0
48	4	.scheduled_events	Scheduled events	1	\N	\N	0
49	4	.project.condition_survey	Condition Survey	1	1	fm_condition_survey	1
50	4	.org_unit	Org unit	0	1	fm_org_unit	0
51	4	.ticket.category	Categories	1	\N	\N	0
52	4	.project.category	Categories	1	\N	\N	0
53	4	.document.category	Categories	1	\N	\N	0
54	4	.vendor.category	Categories	1	\N	\N	0
55	4	.tenant.category	Categories	1	\N	\N	0
56	4	.owner.category	Categories	1	\N	\N	0
57	4	.report	Generic report	1	\N	\N	0
58	4	.location.exception	location exception	1	\N	\N	0
59	4	.document.category.1	Picture	1	\N	\N	0
60	4	.document.category.2	Report	1	\N	\N	0
61	4	.document.category.3	Instruction	1	\N	\N	0
62	5	run	Automatically added on install - run addressbook	0	\N	\N	0
63	5	admin	Allow app admins - addressbook	0	\N	\N	0
64	5	org_person	Allow custom fields on relation org_person	0	1	phpgw_contact_org_person	0
65	5	person	Allow custom fields on table person	0	1	phpgw_contact_person	0
66	5	organisation	Allow custom fields on table org	0	1	phpgw_contact_org	0
67	6	run	Automatically added on install - run controller	0	\N	\N	0
68	6	admin	Allow app admins - controller	0	\N	\N	0
69	6	.	Root	0	\N	\N	0
70	6	.usertype	Usertypes	0	\N	\N	0
71	6	.usertype.superuser	Usertype: Superuser	0	\N	\N	0
72	6	.usertype.user	Usertype: User	0	\N	\N	0
73	6	.control	Control	1	\N	\N	0
74	6	.checklist	Checklist	1	\N	\N	0
75	6	.procedure	Procedure	1	\N	\N	0
76	7	run	Automatically added on install - run rental	0	\N	\N	0
78	7	.	Root	0	\N	\N	0
79	7	.contract	Contract	0	\N	\N	1
80	7	.application	Application	0	\N	\N	1
81	7	.moveout	Moveout	1	1	rental_moveout	1
82	7	.movein	Movein	1	1	rental_movein	1
83	7	.ORG	Locations for organisational units	0	\N	\N	0
84	7	.ORG.BK	Organisational units in Bergen Kommune	0	\N	\N	0
85	7	.RESPONSIBILITY	Fields of responsibilities	0	\N	\N	0
86	7	.RESPONSIBILITY.INTERNAL	Field of responsibility: internleie	0	\N	\N	0
87	7	.RESPONSIBILITY.INTO	Field of responsibility: innleie	0	\N	\N	0
88	7	.RESPONSIBILITY.OUT	Field of responsibility: utleie	0	\N	\N	0
89	8	run	Automatically added on install - run frontend	0	\N	\N	0
90	8	admin	Allow app admins - frontend	0	\N	\N	0
91	8	.	top	0	\N	\N	0
92	8	.ticket	helpdesk	0	\N	\N	0
93	8	.rental.contract	contract_internal	0	\N	\N	0
94	8	.rental.contract_in	contract_in	0	\N	\N	0
95	8	.rental.contract_ex	contract_ex	0	\N	\N	0
96	8	.document.drawings	drawings	0	\N	\N	0
97	8	.document.pictures	pictures	0	\N	\N	0
98	8	.document.contracts	contract_documents	0	\N	\N	0
99	8	.property.maintenance	maintenance	0	\N	\N	0
100	8	.property.refurbishment	refurbishment	0	\N	\N	0
101	8	.property.services	services	0	\N	\N	0
102	8	.delegates	delegates	0	\N	\N	0
103	8	.controller	controller	0	\N	\N	0
104	9	run	Automatically added on install - run mobilefrontend	0	\N	\N	0
105	9	admin	Allow app admins - mobilefrontend	0	\N	\N	0
106	4	.admin_booking	Administrer booking	1	\N	\N	0
107	4	.ticket.category.4	Test category 1	1	\N	\N	0
108	4	.ticket.category.5	Test category 2	1	\N	\N	0
\.


--
-- Data for Name: phpgw_log; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_log (log_id, log_date, log_account_id, log_account_lid, log_app, log_severity, log_file, log_line, log_msg) FROM stdin;
1	2018-01-11 08:56:56	1002	sysadmin	controller	W 	dummy	0	Attempted to access ''controller''
2	2018-01-12 12:44:47	1002	sysadmin	frontend	F 	/path/to/phpgroupware/phpgwapi/inc/class.db_pdo.inc.php	464	Error: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "phpgw_messenger_messages" does not exist\nLINE 1: select count(*) as cnt from phpgw_messenger_messages where m...\n                                    ^<br>SQL: select count(*) as cnt from phpgw_messenger_messages where message_owner=''1002''  AND message_status = ''N''\n in File: /var/www/html/portico/messenger/inc/class.somessenger_sql.inc.php\n on Line: 103\n\n&nbsp;#0\tcreateObject(frontend.uicontroller) [/var/www/html/portico/index.php:95]\n#1\tphpgwapi_object_factory::createObject(frontend_uicontroller, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_) [/var/www/html/portico/phpgwapi/inc/common_functions.inc.php:243]\n#2\tfrontend_uicontroller->__construct() [/var/www/html/portico/phpgwapi/inc/class.object_factory.inc.php:90]\n#3\tfrontend_uicommon->__construct() [/var/www/html/portico/frontend/inc/class.uicontroller.inc.php:54]\n#4\tbomessenger->total_messages( AND message_status = ''N'') [/var/www/html/portico/frontend/inc/class.uicommon.inc.php:272]\n#5\tsomessenger->total_messages( AND message_status = ''N'') [/var/www/html/portico/messenger/inc/class.bomessenger.inc.php:411]\n#6\tphpgwapi_db_pdo->query(select count(*) as cnt from phpgw_messenger_messages where message_owner=''1002''  AND message_status = ''N'', 103, /var/www/html/portico/messenger/inc/class.somessenger_sql.inc.php) [/var/www/html/portico/messenger/inc/class.somessenger_sql.inc.php:103]\n#7\ttrigger_error(Error: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "phpgw_messenger_messages" does not exist\nLINE 1: select count(*) as cnt from phpgw_messenger_messages where m...\n                                    ^<br>SQL: select count(*) as cnt from phpgw_messenger_messages where message_owner=''1002''  AND message_status = ''N''\n in File: /var/www/html/portico/messenger/inc/class.somessenger_sql.inc.php\n on Line: 103\n, 256) [/var/www/html/portico/phpgwapi/inc/class.db_pdo.inc.php:464]
3	2018-01-12 12:45:09	1002	sysadmin	frontend	F 	/path/to/phpgroupware/phpgwapi/inc/class.db_pdo.inc.php	464	Error: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "phpgw_messenger_messages" does not exist\nLINE 1: select count(*) as cnt from phpgw_messenger_messages where m...\n                                    ^<br>SQL: select count(*) as cnt from phpgw_messenger_messages where message_owner=''1002''  AND message_status = ''N''\n in File: /var/www/html/portico/messenger/inc/class.somessenger_sql.inc.php\n on Line: 103\n\n&nbsp;#0\tcreateObject(frontend.uidelegates) [/var/www/html/portico/index.php:95]\n#1\tphpgwapi_object_factory::createObject(frontend_uidelegates, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_, _UNDEF_) [/var/www/html/portico/phpgwapi/inc/common_functions.inc.php:243]\n#2\tfrontend_uidelegates->__construct() [/var/www/html/portico/phpgwapi/inc/class.object_factory.inc.php:90]\n#3\tfrontend_uicommon->__construct() [/var/www/html/portico/frontend/inc/class.uidelegates.inc.php:17]\n#4\tbomessenger->total_messages( AND message_status = ''N'') [/var/www/html/portico/frontend/inc/class.uicommon.inc.php:272]\n#5\tsomessenger->total_messages( AND message_status = ''N'') [/var/www/html/portico/messenger/inc/class.bomessenger.inc.php:411]\n#6\tphpgwapi_db_pdo->query(select count(*) as cnt from phpgw_messenger_messages where message_owner=''1002''  AND message_status = ''N'', 103, /var/www/html/portico/messenger/inc/class.somessenger_sql.inc.php) [/var/www/html/portico/messenger/inc/class.somessenger_sql.inc.php:103]\n#7\ttrigger_error(Error: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "phpgw_messenger_messages" does not exist\nLINE 1: select count(*) as cnt from phpgw_messenger_messages where m...\n                                    ^<br>SQL: select count(*) as cnt from phpgw_messenger_messages where message_owner=''1002''  AND message_status = ''N''\n in File: /var/www/html/portico/messenger/inc/class.somessenger_sql.inc.php\n on Line: 103\n, 256) [/var/www/html/portico/phpgwapi/inc/class.db_pdo.inc.php:464]
\.


--
-- Data for Name: phpgw_mail_handler; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_mail_handler (handler_id, target_email, handler, is_active, lastmod, lastmod_user) FROM stdin;
\.


--
-- Data for Name: phpgw_mapping; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_mapping (ext_user, auth_type, status, location, account_lid) FROM stdin;
\.


--
-- Data for Name: phpgw_nextid; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_nextid (id, appname) FROM stdin;
1003	groups
1008	accounts
\.


--
-- Data for Name: phpgw_notification; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_notification (id, location_id, location_item_id, contact_id, is_active, notification_method, user_id, entry_date) FROM stdin;
\.


--
-- Data for Name: phpgw_preferences; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_preferences (preference_owner, preference_app, preference_value) FROM stdin;
-2	common	a:12:{s:9:"maxmatchs";i:10;s:12:"template_set";s:7:"portico";s:5:"theme";s:7:"portico";s:9:"tz_offset";i:0;s:10:"dateformat";s:5:"Y/m/d";s:4:"lang";s:2:"no";s:10:"timeformat";i:24;s:11:"default_app";s:0:"";s:8:"currency";s:1:"$";s:9:"show_help";i:0;s:15:"account_display";s:8:"lastname";s:8:"rteditor";s:8:"ckeditor";}
-2	addressbook	a:0:{}
-2	calendar	a:5:{s:13:"workdaystarts";i:9;s:11:"workdayends";i:17;s:13:"weekdaystarts";s:6:"Monday";s:15:"defaultcalendar";s:5:"month";s:24:"planner_start_with_group";i:1000;}
1002	portal_order	a:1:{i:0;s:1:"6";}
\.


--
-- Data for Name: phpgw_sessions; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_sessions (session_id, ip, data, lastmodts) FROM stdin;
\.


--
-- Data for Name: phpgw_vfs; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_vfs (file_id, owner_id, createdby_id, modifiedby_id, created, modified, size, mime_type, deleteable, comment, app, directory, name, link_directory, link_name, version, content, external_id, md5_sum) FROM stdin;
1	0	0	0	1970-01-01 00:00:00	\N	\N	Directory	Y	\N	\N	/		\N	\N	0.0.0.0	\N	\N	\N
2	0	0	0	1970-01-01 00:00:00	\N	\N	Directory	Y	\N	\N	/	home	\N	\N	0.0.0.0	\N	\N	\N
\.


--
-- Data for Name: phpgw_vfs_file_relation; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_vfs_file_relation (relation_id, file_id, location_id, location_item_id, is_private, account_id, entry_date, start_date, end_date) FROM stdin;
\.


--
-- Data for Name: phpgw_vfs_filedata; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.phpgw_vfs_filedata (file_id, metadata) FROM stdin;
\.


--
-- Data for Name: rental_adjustment; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_adjustment (id, price_item_id, responsibility_id, adjustment_date, adjustment_type, new_price, percent_, adjustment_interval, is_manual, extra_adjustment, is_executed) FROM stdin;
\.


--
-- Data for Name: rental_application; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_application (id, ecodimb_id, district_id, composite_type_id, cleaning, payment_method, date_start, date_end, assign_date_start, assign_date_end, entry_date, identifier, adjustment_type, firstname, lastname, job_title, company_name, department, address1, address2, postal_code, place, phone, email, account_number, unit_leader, status, executive_officer) FROM stdin;
\.


--
-- Data for Name: rental_application_comment; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_application_comment (id, application_id, "time", author, comment, type) FROM stdin;
\.


--
-- Data for Name: rental_application_composite; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_application_composite (id, application_id, composite_id) FROM stdin;
\.


--
-- Data for Name: rental_billing; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_billing (id, total_sum, success, created_by, timestamp_start, timestamp_stop, timestamp_commit, location_id, title, deleted, export_format, export_data, serial_start, serial_end) FROM stdin;
\.


--
-- Data for Name: rental_billing_info; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_billing_info (id, billing_id, location_id, term_id, year, month, deleted) FROM stdin;
\.


--
-- Data for Name: rental_billing_term; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_billing_term (id, title, months) FROM stdin;
1	monthly	1
2	annually	12
3	half-year	6
4	free_of_charge	0
\.


--
-- Data for Name: rental_composite; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_composite (id, name, description, is_active, status_id, address_1, address_2, house_number, postcode, place, has_custom_address, object_type_id, composite_type_id, area, furnish_type_id, standard_id, part_of_town_id, custom_price_factor, custom_price, price_type_id) FROM stdin;
\.


--
-- Data for Name: rental_composite_standard; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_composite_standard (id, name, factor) FROM stdin;
\.


--
-- Data for Name: rental_composite_type; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_composite_type (id, name) FROM stdin;
1	Type 1
2	Type 2
\.


--
-- Data for Name: rental_contract; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_contract (id, date_start, date_end, billing_start, billing_end, location_id, term_id, security_type, security_amount, old_contract_id, executive_officer, created, created_by, comment, last_updated, service_id, responsibility_id, reference, customer_order_id, invoice_header, account_in, account_out, project_id, due_date, contract_type_id, rented_area, adjustment_interval, adjustment_share, adjustment_year, adjustable, override_adjustment_start, publish_comment, notify_on_expire, notified_time) FROM stdin;
\.


--
-- Data for Name: rental_contract_composite; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_contract_composite (id, contract_id, composite_id) FROM stdin;
\.


--
-- Data for Name: rental_contract_last_edited; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_contract_last_edited (contract_id, account_id, edited_on) FROM stdin;
\.


--
-- Data for Name: rental_contract_party; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_contract_party (contract_id, party_id, is_payer) FROM stdin;
\.


--
-- Data for Name: rental_contract_price_item; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_contract_price_item (id, price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end, is_billed, is_one_time, billing_id) FROM stdin;
\.


--
-- Data for Name: rental_contract_responsibility; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_contract_responsibility (id, location_id, title, notify_before, notify_before_due_date, notify_after_termination_date, account_in, account_out, project_number, agresso_export_format) FROM stdin;
1	86	contract_type_internleie	183	183	366	119001	119001	9	agresso_gl07
2	87	contract_type_innleie	183	183	366	\N	\N	\N	\N
3	88	contract_type_eksternleie	183	183	366	\N	1510	\N	agresso_lg04
\.


--
-- Data for Name: rental_contract_responsibility_unit; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_contract_responsibility_unit (id, name) FROM stdin;
\.


--
-- Data for Name: rental_contract_types; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_contract_types (id, label, responsibility_id, account) FROM stdin;
1	contract_type_internleie_egne	1	\N
2	contract_type_internleie_innleie	1	\N
3	contract_type_internleie_investeringskontrakt	1	\N
4	contract_type_internleie_KF	1	\N
5	contract_type_internleie_andre	1	\N
6	contract_type_eksternleie_feste	3	1520
7	contract_type_eksternleie_leilighet	3	1530
8	contract_type_eksternleie_annen	3	1510
\.


--
-- Data for Name: rental_document; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_document (id, name, contract_id, party_id, title, description, type_id) FROM stdin;
\.


--
-- Data for Name: rental_document_types; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_document_types (id, title) FROM stdin;
1	contracts
2	fire_drawings
3	calculations_internal_investment
\.


--
-- Data for Name: rental_email_out; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_email_out (id, name, remark, subject, content, user_id, created, modified) FROM stdin;
\.


--
-- Data for Name: rental_email_out_party; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_email_out_party (id, email_out_id, party_id, status) FROM stdin;
\.


--
-- Data for Name: rental_email_template; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_email_template (id, name, content, public_, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: rental_invoice; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_invoice (id, contract_id, billing_id, party_id, timestamp_created, timestamp_start, timestamp_end, total_sum, total_area, header, account_in, account_out, service_id, responsibility_id, project_id, serial_number) FROM stdin;
\.


--
-- Data for Name: rental_invoice_price_item; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_invoice_price_item (id, invoice_id, title, area, count, agresso_id, is_area, is_one_time, price, total_price, date_start, date_end) FROM stdin;
\.


--
-- Data for Name: rental_location_factor; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_location_factor (id, part_of_town_id, factor, remark, user_id, entry_date, modified_date) FROM stdin;
\.


--
-- Data for Name: rental_movein; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_movein (id, contract_id, account_id, created, modified) FROM stdin;
\.


--
-- Data for Name: rental_movein_comment; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_movein_comment (id, movein_id, "time", author, comment, type) FROM stdin;
\.


--
-- Data for Name: rental_moveout; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_moveout (id, contract_id, account_id, created, modified) FROM stdin;
\.


--
-- Data for Name: rental_moveout_comment; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_moveout_comment (id, moveout_id, "time", author, comment, type) FROM stdin;
\.


--
-- Data for Name: rental_notification; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_notification (id, location_id, account_id, contract_id, message, date, last_notified, recurrence, deleted) FROM stdin;
\.


--
-- Data for Name: rental_notification_workbench; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_notification_workbench (id, account_id, date, notification_id, workbench_message, dismissed) FROM stdin;
\.


--
-- Data for Name: rental_party; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_party (id, identifier, customer_id, first_name, last_name, comment, is_inactive, title, company_name, department, address_1, address_2, postal_code, place, phone, mobile_phone, fax, email, url, account_number, reskontro, location_id, result_unit_number, org_enhet_id, unit_leader) FROM stdin;
\.


--
-- Data for Name: rental_price_item; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_price_item (id, title, agresso_id, is_area, is_inactive, is_adjustable, standard, price, responsibility_id, type) FROM stdin;
1	Unknown	UNKNOWN	f	f	f	f	0.00	0	1
2	Leie	INNLEIE	f	f	f	f	0.00	87	1
\.


--
-- Data for Name: rental_unit; Type: TABLE DATA; Schema: public; Owner: portico
--

COPY public.rental_unit (id, composite_id, location_code) FROM stdin;
\.


--
-- Name: seq_controller_check_item; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_check_item', 1, false);


--
-- Name: seq_controller_check_item_case; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_check_item_case', 1, false);


--
-- Name: seq_controller_check_item_status; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_check_item_status', 1, false);


--
-- Name: seq_controller_check_list; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_check_list', 1, false);


--
-- Name: seq_controller_control; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control', 1, false);


--
-- Name: seq_controller_control_component_list; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_component_list', 1, false);


--
-- Name: seq_controller_control_group; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_group', 1, false);


--
-- Name: seq_controller_control_group_component_list; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_group_component_list', 1, false);


--
-- Name: seq_controller_control_group_list; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_group_list', 1, false);


--
-- Name: seq_controller_control_item; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_item', 1, false);


--
-- Name: seq_controller_control_item_list; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_item_list', 1, false);


--
-- Name: seq_controller_control_item_option; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_item_option', 1, false);


--
-- Name: seq_controller_control_location_list; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_location_list', 1, false);


--
-- Name: seq_controller_control_serie; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_serie', 1, false);


--
-- Name: seq_controller_control_serie_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_control_serie_history', 1, false);


--
-- Name: seq_controller_document; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_document', 1, false);


--
-- Name: seq_controller_document_types; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_document_types', 1, true);


--
-- Name: seq_controller_procedure; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_controller_procedure', 1, false);


--
-- Name: seq_fm_action_pending; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_action_pending', 1, false);


--
-- Name: seq_fm_action_pending_category; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_action_pending_category', 3, true);


--
-- Name: seq_fm_budget_cost; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_budget_cost', 1, false);


--
-- Name: seq_fm_condition_survey_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_condition_survey_history', 1, false);


--
-- Name: seq_fm_cron_log; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_cron_log', 1, false);


--
-- Name: seq_fm_custom_menu_items; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_custom_menu_items', 1, false);


--
-- Name: seq_fm_document; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_document', 1, false);


--
-- Name: seq_fm_document_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_document_history', 1, false);


--
-- Name: seq_fm_document_relation; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_document_relation', 1, false);


--
-- Name: seq_fm_eco_period_transition; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_eco_period_transition', 1, false);


--
-- Name: seq_fm_eco_periodization_outline; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_eco_periodization_outline', 1, false);


--
-- Name: seq_fm_ecobilag; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_ecobilag', 1, false);


--
-- Name: seq_fm_ecobilag_process_log; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_ecobilag_process_log', 1, false);


--
-- Name: seq_fm_ecodimb_role_user; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_ecodimb_role_user', 1, false);


--
-- Name: seq_fm_ecodimb_role_user_substitute; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_ecodimb_role_user_substitute', 1, false);


--
-- Name: seq_fm_entity_group; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_entity_group', 1, false);


--
-- Name: seq_fm_entity_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_entity_history', 1, false);


--
-- Name: seq_fm_event; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_event', 1, false);


--
-- Name: seq_fm_generic_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_generic_history', 1, false);


--
-- Name: seq_fm_jasper; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_jasper', 1, false);


--
-- Name: seq_fm_jasper_input; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_jasper_input', 1, false);


--
-- Name: seq_fm_jasper_input_type; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_jasper_input_type', 8, true);


--
-- Name: seq_fm_location_contact; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_location_contact', 1, false);


--
-- Name: seq_fm_location_exception; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_location_exception', 1, false);


--
-- Name: seq_fm_location_exception_category; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_location_exception_category', 1, false);


--
-- Name: seq_fm_location_exception_category_text; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_location_exception_category_text', 1, false);


--
-- Name: seq_fm_locations; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_locations', 11, true);


--
-- Name: seq_fm_order_template; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_order_template', 1, false);


--
-- Name: seq_fm_part_of_town; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_part_of_town', 1, true);


--
-- Name: seq_fm_project_buffer_budget; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_project_buffer_budget', 1, false);


--
-- Name: seq_fm_project_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_project_history', 1, false);


--
-- Name: seq_fm_request_consume; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_request_consume', 1, false);


--
-- Name: seq_fm_request_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_request_history', 1, false);


--
-- Name: seq_fm_request_planning; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_request_planning', 1, false);


--
-- Name: seq_fm_response_template; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_response_template', 1, false);


--
-- Name: seq_fm_responsibility; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_responsibility', 1, false);


--
-- Name: seq_fm_responsibility_contact; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_responsibility_contact', 1, false);


--
-- Name: seq_fm_responsibility_role; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_responsibility_role', 1, false);


--
-- Name: seq_fm_s_agreement_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_s_agreement_history', 1, false);


--
-- Name: seq_fm_template; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_template', 1, false);


--
-- Name: seq_fm_template_hours; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_template_hours', 1, false);


--
-- Name: seq_fm_tenant_claim; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_tenant_claim', 1, false);


--
-- Name: seq_fm_tenant_claim_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_tenant_claim_history', 1, false);


--
-- Name: seq_fm_tts_budget; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_tts_budget', 1, false);


--
-- Name: seq_fm_tts_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_tts_history', 5, true);


--
-- Name: seq_fm_tts_payments; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_tts_payments', 1, false);


--
-- Name: seq_fm_tts_status; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_tts_status', 1, false);


--
-- Name: seq_fm_tts_tickets; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_tts_tickets', 3, true);


--
-- Name: seq_fm_view_dataset; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_view_dataset', 1, false);


--
-- Name: seq_fm_view_dataset_report; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_view_dataset_report', 1, false);


--
-- Name: seq_fm_wo_hours; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_wo_hours', 1, false);


--
-- Name: seq_fm_workorder_history; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_fm_workorder_history', 1, false);


--
-- Name: seq_phpgw_account_delegates; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_account_delegates', 1, false);


--
-- Name: seq_phpgw_accounts; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_accounts', 1, false);


--
-- Name: seq_phpgw_applications; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_applications', 9, true);


--
-- Name: seq_phpgw_categories; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_categories', 5, true);


--
-- Name: seq_phpgw_contact; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact', 9, true);


--
-- Name: seq_phpgw_contact_addr; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact_addr', 1, false);


--
-- Name: seq_phpgw_contact_addr_type; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact_addr_type', 2, true);


--
-- Name: seq_phpgw_contact_comm; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact_comm', 1, false);


--
-- Name: seq_phpgw_contact_comm_descr; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact_comm_descr', 21, true);


--
-- Name: seq_phpgw_contact_comm_type; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact_comm_type', 7, true);


--
-- Name: seq_phpgw_contact_note; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact_note', 1, false);


--
-- Name: seq_phpgw_contact_note_type; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact_note_type', 3, true);


--
-- Name: seq_phpgw_contact_others; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact_others', 1, false);


--
-- Name: seq_phpgw_contact_types; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_contact_types', 2, true);


--
-- Name: seq_phpgw_history_log; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_history_log', 1, false);


--
-- Name: seq_phpgw_hooks; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_hooks', 102, true);


--
-- Name: seq_phpgw_interlink; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_interlink', 1, false);


--
-- Name: seq_phpgw_interserv; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_interserv', 1, true);


--
-- Name: seq_phpgw_locations; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_locations', 108, true);


--
-- Name: seq_phpgw_log; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_log', 3, true);


--
-- Name: seq_phpgw_mail_handler; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_mail_handler', 1, false);


--
-- Name: seq_phpgw_notification; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_notification', 1, false);


--
-- Name: seq_phpgw_vfs; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_vfs', 2, true);


--
-- Name: seq_phpgw_vfs_file_relation; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_phpgw_vfs_file_relation', 1, false);


--
-- Name: seq_rental_adjustment; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_adjustment', 1, false);


--
-- Name: seq_rental_application; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_application', 1, false);


--
-- Name: seq_rental_application_comment; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_application_comment', 1, false);


--
-- Name: seq_rental_application_composite; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_application_composite', 1, false);


--
-- Name: seq_rental_billing; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_billing', 1, false);


--
-- Name: seq_rental_billing_info; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_billing_info', 1, false);


--
-- Name: seq_rental_billing_term; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_billing_term', 4, true);


--
-- Name: seq_rental_composite; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_composite', 1, false);


--
-- Name: seq_rental_contract_composite; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_contract_composite', 1, false);


--
-- Name: seq_rental_contract_price_item; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_contract_price_item', 1, false);


--
-- Name: seq_rental_contract_responsibility; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_contract_responsibility', 3, true);


--
-- Name: seq_rental_contract_types; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_contract_types', 1, false);


--
-- Name: seq_rental_document; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_document', 1, false);


--
-- Name: seq_rental_document_types; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_document_types', 3, true);


--
-- Name: seq_rental_email_out; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_email_out', 1, false);


--
-- Name: seq_rental_email_out_party; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_email_out_party', 1, false);


--
-- Name: seq_rental_email_template; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_email_template', 1, false);


--
-- Name: seq_rental_invoice; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_invoice', 1, false);


--
-- Name: seq_rental_invoice_price_item; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_invoice_price_item', 1, false);


--
-- Name: seq_rental_location_factor; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_location_factor', 1, false);


--
-- Name: seq_rental_movein; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_movein', 1, false);


--
-- Name: seq_rental_movein_comment; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_movein_comment', 1, false);


--
-- Name: seq_rental_moveout; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_moveout', 1, false);


--
-- Name: seq_rental_moveout_comment; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_moveout_comment', 1, false);


--
-- Name: seq_rental_notification; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_notification', 1, false);


--
-- Name: seq_rental_notification_workbench; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_notification_workbench', 1, false);


--
-- Name: seq_rental_party; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_party', 1, false);


--
-- Name: seq_rental_price_item; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_price_item', 2, true);


--
-- Name: seq_rental_unit; Type: SEQUENCE SET; Schema: public; Owner: portico
--

SELECT pg_catalog.setval('public.seq_rental_unit', 1, false);


--
-- Name: controller_check_item_case controller_check_item_case_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_check_item_case
    ADD CONSTRAINT controller_check_item_case_pkey PRIMARY KEY (id);


--
-- Name: controller_check_item controller_check_item_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_check_item
    ADD CONSTRAINT controller_check_item_pkey PRIMARY KEY (id);


--
-- Name: controller_check_item_status controller_check_item_status_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_check_item_status
    ADD CONSTRAINT controller_check_item_status_pkey PRIMARY KEY (id);


--
-- Name: controller_check_list controller_check_list_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_check_list
    ADD CONSTRAINT controller_check_list_pkey PRIMARY KEY (id);


--
-- Name: controller_control_component_list controller_control_component_list_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_component_list
    ADD CONSTRAINT controller_control_component_list_pkey PRIMARY KEY (id);


--
-- Name: controller_control_group_component_list controller_control_group_component_list_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_group_component_list
    ADD CONSTRAINT controller_control_group_component_list_pkey PRIMARY KEY (id);


--
-- Name: controller_control_group_list controller_control_group_list_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_group_list
    ADD CONSTRAINT controller_control_group_list_pkey PRIMARY KEY (id);


--
-- Name: controller_control_group controller_control_group_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_group
    ADD CONSTRAINT controller_control_group_pkey PRIMARY KEY (id);


--
-- Name: controller_control_item_list controller_control_item_list_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_item_list
    ADD CONSTRAINT controller_control_item_list_pkey PRIMARY KEY (id);


--
-- Name: controller_control_item_option controller_control_item_option_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_item_option
    ADD CONSTRAINT controller_control_item_option_pkey PRIMARY KEY (id);


--
-- Name: controller_control_item controller_control_item_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_item
    ADD CONSTRAINT controller_control_item_pkey PRIMARY KEY (id);


--
-- Name: controller_control_location_list controller_control_location_list_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_location_list
    ADD CONSTRAINT controller_control_location_list_pkey PRIMARY KEY (id);


--
-- Name: controller_control controller_control_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control
    ADD CONSTRAINT controller_control_pkey PRIMARY KEY (id);


--
-- Name: controller_control_serie_history controller_control_serie_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_serie_history
    ADD CONSTRAINT controller_control_serie_history_pkey PRIMARY KEY (id);


--
-- Name: controller_control_serie controller_control_serie_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_serie
    ADD CONSTRAINT controller_control_serie_pkey PRIMARY KEY (id);


--
-- Name: controller_document controller_document_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_document
    ADD CONSTRAINT controller_document_pkey PRIMARY KEY (id);


--
-- Name: controller_document_types controller_document_types_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_document_types
    ADD CONSTRAINT controller_document_types_pkey PRIMARY KEY (id);


--
-- Name: controller_procedure controller_procedure_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_procedure
    ADD CONSTRAINT controller_procedure_pkey PRIMARY KEY (id);


--
-- Name: fm_action_pending_category fm_action_pending_category_num_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_action_pending_category
    ADD CONSTRAINT fm_action_pending_category_num_key UNIQUE (num);


--
-- Name: fm_action_pending_category fm_action_pending_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_action_pending_category
    ADD CONSTRAINT fm_action_pending_category_pkey PRIMARY KEY (id);


--
-- Name: fm_action_pending fm_action_pending_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_action_pending
    ADD CONSTRAINT fm_action_pending_pkey PRIMARY KEY (id);


--
-- Name: fm_activities fm_activities_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_activities
    ADD CONSTRAINT fm_activities_pkey PRIMARY KEY (id);


--
-- Name: fm_activity_price_index fm_activity_price_index_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_activity_price_index
    ADD CONSTRAINT fm_activity_price_index_pkey PRIMARY KEY (activity_id, agreement_id, index_count);


--
-- Name: fm_agreement_group fm_agreement_group_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_agreement_group
    ADD CONSTRAINT fm_agreement_group_pkey PRIMARY KEY (id);


--
-- Name: fm_agreement fm_agreement_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_agreement
    ADD CONSTRAINT fm_agreement_pkey PRIMARY KEY (group_id, id);


--
-- Name: fm_agreement_status fm_agreement_status_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_agreement_status
    ADD CONSTRAINT fm_agreement_status_pkey PRIMARY KEY (id);


--
-- Name: fm_async_method fm_async_method_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_async_method
    ADD CONSTRAINT fm_async_method_pkey PRIMARY KEY (id);


--
-- Name: fm_authorities_demands fm_authorities_demands_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_authorities_demands
    ADD CONSTRAINT fm_authorities_demands_pkey PRIMARY KEY (id);


--
-- Name: fm_b_account_category fm_b_account_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_b_account_category
    ADD CONSTRAINT fm_b_account_category_pkey PRIMARY KEY (id);


--
-- Name: fm_b_account fm_b_account_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_b_account
    ADD CONSTRAINT fm_b_account_pkey PRIMARY KEY (id);


--
-- Name: fm_branch fm_branch_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_branch
    ADD CONSTRAINT fm_branch_pkey PRIMARY KEY (id);


--
-- Name: fm_budget_basis fm_budget_basis_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_budget_basis
    ADD CONSTRAINT fm_budget_basis_pkey PRIMARY KEY (id);


--
-- Name: fm_budget_basis fm_budget_basis_year_b_group_district_id_revision_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_budget_basis
    ADD CONSTRAINT fm_budget_basis_year_b_group_district_id_revision_key UNIQUE (year, b_group, district_id, revision);


--
-- Name: fm_budget_cost fm_budget_cost_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_budget_cost
    ADD CONSTRAINT fm_budget_cost_pkey PRIMARY KEY (id);


--
-- Name: fm_budget_cost fm_budget_cost_year_month_b_account_id_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_budget_cost
    ADD CONSTRAINT fm_budget_cost_year_month_b_account_id_key UNIQUE (year, month, b_account_id);


--
-- Name: fm_budget_period fm_budget_period_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_budget_period
    ADD CONSTRAINT fm_budget_period_pkey PRIMARY KEY (year, month, b_account_id);


--
-- Name: fm_budget fm_budget_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_budget
    ADD CONSTRAINT fm_budget_pkey PRIMARY KEY (id);


--
-- Name: fm_budget fm_budget_year_b_account_id_district_id_revision_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_budget
    ADD CONSTRAINT fm_budget_year_b_account_id_district_id_revision_key UNIQUE (year, b_account_id, district_id, revision);


--
-- Name: fm_building_part fm_building_part_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_building_part
    ADD CONSTRAINT fm_building_part_pkey PRIMARY KEY (id);


--
-- Name: fm_cache fm_cache_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_cache
    ADD CONSTRAINT fm_cache_pkey PRIMARY KEY (name);


--
-- Name: fm_chapter fm_chapter_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_chapter
    ADD CONSTRAINT fm_chapter_pkey PRIMARY KEY (id);


--
-- Name: fm_condition_survey_history fm_condition_survey_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_condition_survey_history
    ADD CONSTRAINT fm_condition_survey_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_condition_survey fm_condition_survey_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_condition_survey
    ADD CONSTRAINT fm_condition_survey_pkey PRIMARY KEY (id);


--
-- Name: fm_condition_survey_status fm_condition_survey_status_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_condition_survey_status
    ADD CONSTRAINT fm_condition_survey_status_pkey PRIMARY KEY (id);


--
-- Name: fm_cron_log fm_cron_log_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_cron_log
    ADD CONSTRAINT fm_cron_log_pkey PRIMARY KEY (id);


--
-- Name: fm_custom_cols fm_custom_cols_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_custom_cols
    ADD CONSTRAINT fm_custom_cols_pkey PRIMARY KEY (custom_id, id);


--
-- Name: fm_custom_menu_items fm_custom_menu_items_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_custom_menu_items
    ADD CONSTRAINT fm_custom_menu_items_pkey PRIMARY KEY (id);


--
-- Name: fm_custom fm_custom_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_custom
    ADD CONSTRAINT fm_custom_pkey PRIMARY KEY (id);


--
-- Name: fm_district fm_district_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_district
    ADD CONSTRAINT fm_district_pkey PRIMARY KEY (id);


--
-- Name: fm_document_history fm_document_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_document_history
    ADD CONSTRAINT fm_document_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_document fm_document_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_document
    ADD CONSTRAINT fm_document_pkey PRIMARY KEY (id);


--
-- Name: fm_document_relation fm_document_relation_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_document_relation
    ADD CONSTRAINT fm_document_relation_pkey PRIMARY KEY (id);


--
-- Name: fm_document_status fm_document_status_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_document_status
    ADD CONSTRAINT fm_document_status_pkey PRIMARY KEY (id);


--
-- Name: fm_eco_period_transition fm_eco_period_transition_month_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_eco_period_transition
    ADD CONSTRAINT fm_eco_period_transition_month_key UNIQUE (month);


--
-- Name: fm_eco_period_transition fm_eco_period_transition_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_eco_period_transition
    ADD CONSTRAINT fm_eco_period_transition_pkey PRIMARY KEY (id);


--
-- Name: fm_eco_periodization_outline fm_eco_periodization_outline_periodization_id_month_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_eco_periodization_outline
    ADD CONSTRAINT fm_eco_periodization_outline_periodization_id_month_key UNIQUE (periodization_id, month);


--
-- Name: fm_eco_periodization_outline fm_eco_periodization_outline_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_eco_periodization_outline
    ADD CONSTRAINT fm_eco_periodization_outline_pkey PRIMARY KEY (id);


--
-- Name: fm_eco_periodization fm_eco_periodization_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_eco_periodization
    ADD CONSTRAINT fm_eco_periodization_pkey PRIMARY KEY (id);


--
-- Name: fm_eco_service fm_eco_service_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_eco_service
    ADD CONSTRAINT fm_eco_service_pkey PRIMARY KEY (id);


--
-- Name: fm_ecoart fm_ecoart_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecoart
    ADD CONSTRAINT fm_ecoart_pkey PRIMARY KEY (id);


--
-- Name: fm_ecoavvik fm_ecoavvik_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecoavvik
    ADD CONSTRAINT fm_ecoavvik_pkey PRIMARY KEY (bilagsnr);


--
-- Name: fm_ecobilag_category fm_ecobilag_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecobilag_category
    ADD CONSTRAINT fm_ecobilag_category_pkey PRIMARY KEY (id);


--
-- Name: fm_ecobilag fm_ecobilag_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecobilag
    ADD CONSTRAINT fm_ecobilag_pkey PRIMARY KEY (id);


--
-- Name: fm_ecobilag_process_code fm_ecobilag_process_code_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecobilag_process_code
    ADD CONSTRAINT fm_ecobilag_process_code_pkey PRIMARY KEY (id);


--
-- Name: fm_ecobilag_process_log fm_ecobilag_process_log_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecobilag_process_log
    ADD CONSTRAINT fm_ecobilag_process_log_pkey PRIMARY KEY (id);


--
-- Name: fm_ecobilagkilde fm_ecobilagkilde_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecobilagkilde
    ADD CONSTRAINT fm_ecobilagkilde_pkey PRIMARY KEY (id);


--
-- Name: fm_ecobilagoverf fm_ecobilagoverf_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecobilagoverf
    ADD CONSTRAINT fm_ecobilagoverf_pkey PRIMARY KEY (id);


--
-- Name: fm_ecodimb fm_ecodimb_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecodimb
    ADD CONSTRAINT fm_ecodimb_pkey PRIMARY KEY (id);


--
-- Name: fm_ecodimb_role fm_ecodimb_role_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecodimb_role
    ADD CONSTRAINT fm_ecodimb_role_pkey PRIMARY KEY (id);


--
-- Name: fm_ecodimb_role_user fm_ecodimb_role_user_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecodimb_role_user
    ADD CONSTRAINT fm_ecodimb_role_user_pkey PRIMARY KEY (id);


--
-- Name: fm_ecodimb_role_user_substitute fm_ecodimb_role_user_substitute_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecodimb_role_user_substitute
    ADD CONSTRAINT fm_ecodimb_role_user_substitute_pkey PRIMARY KEY (id);


--
-- Name: fm_ecodimd fm_ecodimd_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecodimd
    ADD CONSTRAINT fm_ecodimd_pkey PRIMARY KEY (id);


--
-- Name: fm_ecomva fm_ecomva_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecomva
    ADD CONSTRAINT fm_ecomva_pkey PRIMARY KEY (id);


--
-- Name: fm_ecouser fm_ecouser_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecouser
    ADD CONSTRAINT fm_ecouser_pkey PRIMARY KEY (id);


--
-- Name: fm_entity_category fm_entity_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_entity_category
    ADD CONSTRAINT fm_entity_category_pkey PRIMARY KEY (entity_id, id);


--
-- Name: fm_entity_group fm_entity_group_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_entity_group
    ADD CONSTRAINT fm_entity_group_pkey PRIMARY KEY (id);


--
-- Name: fm_entity_history fm_entity_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_entity_history
    ADD CONSTRAINT fm_entity_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_entity_lookup fm_entity_lookup_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_entity_lookup
    ADD CONSTRAINT fm_entity_lookup_pkey PRIMARY KEY (entity_id, location, type);


--
-- Name: fm_entity fm_entity_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_entity
    ADD CONSTRAINT fm_entity_pkey PRIMARY KEY (id);


--
-- Name: fm_event_action fm_event_action_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_event_action
    ADD CONSTRAINT fm_event_action_pkey PRIMARY KEY (id);


--
-- Name: fm_event_exception fm_event_exception_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_event_exception
    ADD CONSTRAINT fm_event_exception_pkey PRIMARY KEY (event_id, exception_time);


--
-- Name: fm_event fm_event_location_id_location_item_id_attrib_id_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_event
    ADD CONSTRAINT fm_event_location_id_location_item_id_attrib_id_key UNIQUE (location_id, location_item_id, attrib_id);


--
-- Name: fm_event fm_event_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_event
    ADD CONSTRAINT fm_event_pkey PRIMARY KEY (id);


--
-- Name: fm_event_receipt fm_event_receipt_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_event_receipt
    ADD CONSTRAINT fm_event_receipt_pkey PRIMARY KEY (event_id, receipt_time);


--
-- Name: fm_event_schedule fm_event_schedule_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_event_schedule
    ADD CONSTRAINT fm_event_schedule_pkey PRIMARY KEY (event_id, schedule_time);


--
-- Name: fm_external_project fm_external_project_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_external_project
    ADD CONSTRAINT fm_external_project_pkey PRIMARY KEY (id);


--
-- Name: fm_gab_location fm_gab_location_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_gab_location
    ADD CONSTRAINT fm_gab_location_pkey PRIMARY KEY (gab_id, location_code);


--
-- Name: fm_generic_history fm_generic_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_generic_history
    ADD CONSTRAINT fm_generic_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_idgenerator fm_idgenerator_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_idgenerator
    ADD CONSTRAINT fm_idgenerator_pkey PRIMARY KEY (name, start_date);


--
-- Name: fm_investment fm_investment_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_investment
    ADD CONSTRAINT fm_investment_pkey PRIMARY KEY (entity_id, invest_id);


--
-- Name: fm_investment_value fm_investment_value_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_investment_value
    ADD CONSTRAINT fm_investment_value_pkey PRIMARY KEY (entity_id, invest_id, index_count);


--
-- Name: fm_jasper_format_type fm_jasper_format_type_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_jasper_format_type
    ADD CONSTRAINT fm_jasper_format_type_pkey PRIMARY KEY (id);


--
-- Name: fm_jasper_input fm_jasper_input_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_jasper_input
    ADD CONSTRAINT fm_jasper_input_pkey PRIMARY KEY (id);


--
-- Name: fm_jasper_input_type fm_jasper_input_type_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_jasper_input_type
    ADD CONSTRAINT fm_jasper_input_type_pkey PRIMARY KEY (id);


--
-- Name: fm_jasper fm_jasper_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_jasper
    ADD CONSTRAINT fm_jasper_pkey PRIMARY KEY (id);


--
-- Name: fm_key_loc fm_key_loc_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_key_loc
    ADD CONSTRAINT fm_key_loc_pkey PRIMARY KEY (id);


--
-- Name: fm_location1_category fm_location1_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location1_category
    ADD CONSTRAINT fm_location1_category_pkey PRIMARY KEY (id);


--
-- Name: fm_location1 fm_location1_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location1
    ADD CONSTRAINT fm_location1_pkey PRIMARY KEY (loc1);


--
-- Name: fm_location2_category fm_location2_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location2_category
    ADD CONSTRAINT fm_location2_category_pkey PRIMARY KEY (id);


--
-- Name: fm_location2 fm_location2_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location2
    ADD CONSTRAINT fm_location2_pkey PRIMARY KEY (loc1, loc2);


--
-- Name: fm_location3_category fm_location3_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location3_category
    ADD CONSTRAINT fm_location3_category_pkey PRIMARY KEY (id);


--
-- Name: fm_location3 fm_location3_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location3
    ADD CONSTRAINT fm_location3_pkey PRIMARY KEY (loc1, loc2, loc3);


--
-- Name: fm_location4_category fm_location4_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location4_category
    ADD CONSTRAINT fm_location4_category_pkey PRIMARY KEY (id);


--
-- Name: fm_location4 fm_location4_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location4
    ADD CONSTRAINT fm_location4_pkey PRIMARY KEY (loc1, loc2, loc3, loc4);


--
-- Name: fm_location_config fm_location_config_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_config
    ADD CONSTRAINT fm_location_config_pkey PRIMARY KEY (column_name);


--
-- Name: fm_location_contact fm_location_contact_contact_id_location_code_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_contact
    ADD CONSTRAINT fm_location_contact_contact_id_location_code_key UNIQUE (contact_id, location_code);


--
-- Name: fm_location_contact fm_location_contact_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_contact
    ADD CONSTRAINT fm_location_contact_pkey PRIMARY KEY (id);


--
-- Name: fm_location_exception_category fm_location_exception_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_exception_category
    ADD CONSTRAINT fm_location_exception_category_pkey PRIMARY KEY (id);


--
-- Name: fm_location_exception_category_text fm_location_exception_category_text_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_exception_category_text
    ADD CONSTRAINT fm_location_exception_category_text_pkey PRIMARY KEY (id);


--
-- Name: fm_location_exception fm_location_exception_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_exception
    ADD CONSTRAINT fm_location_exception_pkey PRIMARY KEY (id);


--
-- Name: fm_location_exception_severity fm_location_exception_severity_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_exception_severity
    ADD CONSTRAINT fm_location_exception_severity_pkey PRIMARY KEY (id);


--
-- Name: fm_location_type fm_location_type_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_type
    ADD CONSTRAINT fm_location_type_pkey PRIMARY KEY (id);


--
-- Name: fm_locations fm_locations_location_code_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_locations
    ADD CONSTRAINT fm_locations_location_code_key UNIQUE (location_code);


--
-- Name: fm_locations fm_locations_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_locations
    ADD CONSTRAINT fm_locations_pkey PRIMARY KEY (id);


--
-- Name: fm_ns3420 fm_ns3420_num_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ns3420
    ADD CONSTRAINT fm_ns3420_num_key UNIQUE (num);


--
-- Name: fm_ns3420 fm_ns3420_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ns3420
    ADD CONSTRAINT fm_ns3420_pkey PRIMARY KEY (id);


--
-- Name: fm_order_dim1 fm_order_dim1_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_order_dim1
    ADD CONSTRAINT fm_order_dim1_pkey PRIMARY KEY (id);


--
-- Name: fm_order_template fm_order_template_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_order_template
    ADD CONSTRAINT fm_order_template_pkey PRIMARY KEY (id);


--
-- Name: fm_orders fm_orders_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_orders
    ADD CONSTRAINT fm_orders_pkey PRIMARY KEY (id);


--
-- Name: fm_org_unit fm_org_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_org_unit
    ADD CONSTRAINT fm_org_unit_pkey PRIMARY KEY (id);


--
-- Name: fm_owner_category fm_owner_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_owner_category
    ADD CONSTRAINT fm_owner_category_pkey PRIMARY KEY (id);


--
-- Name: fm_owner fm_owner_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_owner
    ADD CONSTRAINT fm_owner_pkey PRIMARY KEY (id);


--
-- Name: fm_part_of_town fm_part_of_town_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_part_of_town
    ADD CONSTRAINT fm_part_of_town_pkey PRIMARY KEY (id);


--
-- Name: fm_project_budget fm_project_budget_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_project_budget
    ADD CONSTRAINT fm_project_budget_pkey PRIMARY KEY (project_id, year, month);


--
-- Name: fm_project_buffer_budget fm_project_buffer_budget_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_project_buffer_budget
    ADD CONSTRAINT fm_project_buffer_budget_pkey PRIMARY KEY (id);


--
-- Name: fm_project_history fm_project_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_project_history
    ADD CONSTRAINT fm_project_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_project fm_project_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_project
    ADD CONSTRAINT fm_project_pkey PRIMARY KEY (id);


--
-- Name: fm_project_status fm_project_status_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_project_status
    ADD CONSTRAINT fm_project_status_pkey PRIMARY KEY (id);


--
-- Name: fm_projectbranch fm_projectbranch_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_projectbranch
    ADD CONSTRAINT fm_projectbranch_pkey PRIMARY KEY (project_id, branch_id);


--
-- Name: fm_regulations fm_regulations_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_regulations
    ADD CONSTRAINT fm_regulations_pkey PRIMARY KEY (id);


--
-- Name: fm_request_condition fm_request_condition_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request_condition
    ADD CONSTRAINT fm_request_condition_pkey PRIMARY KEY (request_id, condition_type);


--
-- Name: fm_request_condition_type fm_request_condition_type_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request_condition_type
    ADD CONSTRAINT fm_request_condition_type_pkey PRIMARY KEY (id);


--
-- Name: fm_request_consume fm_request_consume_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request_consume
    ADD CONSTRAINT fm_request_consume_pkey PRIMARY KEY (id);


--
-- Name: fm_request_history fm_request_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request_history
    ADD CONSTRAINT fm_request_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_request fm_request_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request
    ADD CONSTRAINT fm_request_pkey PRIMARY KEY (id);


--
-- Name: fm_request_planning fm_request_planning_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request_planning
    ADD CONSTRAINT fm_request_planning_pkey PRIMARY KEY (id);


--
-- Name: fm_request_responsible_unit fm_request_responsible_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request_responsible_unit
    ADD CONSTRAINT fm_request_responsible_unit_pkey PRIMARY KEY (id);


--
-- Name: fm_request_status fm_request_status_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request_status
    ADD CONSTRAINT fm_request_status_pkey PRIMARY KEY (id);


--
-- Name: fm_response_template fm_response_template_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_response_template
    ADD CONSTRAINT fm_response_template_pkey PRIMARY KEY (id);


--
-- Name: fm_responsibility_contact fm_responsibility_contact_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility_contact
    ADD CONSTRAINT fm_responsibility_contact_pkey PRIMARY KEY (id);


--
-- Name: fm_responsibility_module fm_responsibility_module_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility_module
    ADD CONSTRAINT fm_responsibility_module_pkey PRIMARY KEY (responsibility_id, location_id, cat_id);


--
-- Name: fm_responsibility fm_responsibility_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility
    ADD CONSTRAINT fm_responsibility_pkey PRIMARY KEY (id);


--
-- Name: fm_responsibility_role fm_responsibility_role_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility_role
    ADD CONSTRAINT fm_responsibility_role_pkey PRIMARY KEY (id);


--
-- Name: fm_s_agreement_budget fm_s_agreement_budget_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_s_agreement_budget
    ADD CONSTRAINT fm_s_agreement_budget_pkey PRIMARY KEY (agreement_id, year);


--
-- Name: fm_s_agreement_category fm_s_agreement_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_s_agreement_category
    ADD CONSTRAINT fm_s_agreement_category_pkey PRIMARY KEY (id);


--
-- Name: fm_s_agreement_detail fm_s_agreement_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_s_agreement_detail
    ADD CONSTRAINT fm_s_agreement_detail_pkey PRIMARY KEY (agreement_id, id);


--
-- Name: fm_s_agreement_history fm_s_agreement_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_s_agreement_history
    ADD CONSTRAINT fm_s_agreement_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_s_agreement fm_s_agreement_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_s_agreement
    ADD CONSTRAINT fm_s_agreement_pkey PRIMARY KEY (id);


--
-- Name: fm_s_agreement_pricing fm_s_agreement_pricing_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_s_agreement_pricing
    ADD CONSTRAINT fm_s_agreement_pricing_pkey PRIMARY KEY (agreement_id, item_id, id);


--
-- Name: fm_standard_unit fm_standard_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_standard_unit
    ADD CONSTRAINT fm_standard_unit_pkey PRIMARY KEY (id);


--
-- Name: fm_streetaddress fm_streetaddress_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_streetaddress
    ADD CONSTRAINT fm_streetaddress_pkey PRIMARY KEY (id);


--
-- Name: fm_template_hours fm_template_hours_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_template_hours
    ADD CONSTRAINT fm_template_hours_pkey PRIMARY KEY (id);


--
-- Name: fm_template fm_template_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_template
    ADD CONSTRAINT fm_template_pkey PRIMARY KEY (id);


--
-- Name: fm_tenant_category fm_tenant_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tenant_category
    ADD CONSTRAINT fm_tenant_category_pkey PRIMARY KEY (id);


--
-- Name: fm_tenant_claim_category fm_tenant_claim_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tenant_claim_category
    ADD CONSTRAINT fm_tenant_claim_category_pkey PRIMARY KEY (id);


--
-- Name: fm_tenant_claim_history fm_tenant_claim_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tenant_claim_history
    ADD CONSTRAINT fm_tenant_claim_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_tenant_claim fm_tenant_claim_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tenant_claim
    ADD CONSTRAINT fm_tenant_claim_pkey PRIMARY KEY (id);


--
-- Name: fm_tenant fm_tenant_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tenant
    ADD CONSTRAINT fm_tenant_pkey PRIMARY KEY (id);


--
-- Name: fm_tts_budget fm_tts_budget_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tts_budget
    ADD CONSTRAINT fm_tts_budget_pkey PRIMARY KEY (id);


--
-- Name: fm_tts_history fm_tts_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tts_history
    ADD CONSTRAINT fm_tts_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_tts_payments fm_tts_payments_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tts_payments
    ADD CONSTRAINT fm_tts_payments_pkey PRIMARY KEY (id);


--
-- Name: fm_tts_priority fm_tts_priority_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tts_priority
    ADD CONSTRAINT fm_tts_priority_pkey PRIMARY KEY (id);


--
-- Name: fm_tts_status fm_tts_status_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tts_status
    ADD CONSTRAINT fm_tts_status_pkey PRIMARY KEY (id);


--
-- Name: fm_tts_tickets fm_tts_tickets_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tts_tickets
    ADD CONSTRAINT fm_tts_tickets_pkey PRIMARY KEY (id);


--
-- Name: fm_unspsc_code fm_unspsc_code_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_unspsc_code
    ADD CONSTRAINT fm_unspsc_code_pkey PRIMARY KEY (id);


--
-- Name: fm_vendor_category fm_vendor_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_vendor_category
    ADD CONSTRAINT fm_vendor_category_pkey PRIMARY KEY (id);


--
-- Name: fm_vendor fm_vendor_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_vendor
    ADD CONSTRAINT fm_vendor_pkey PRIMARY KEY (id);


--
-- Name: fm_view_dataset fm_view_dataset_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_view_dataset
    ADD CONSTRAINT fm_view_dataset_pkey PRIMARY KEY (id);


--
-- Name: fm_view_dataset_report fm_view_dataset_report_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_view_dataset_report
    ADD CONSTRAINT fm_view_dataset_report_pkey PRIMARY KEY (id);


--
-- Name: fm_wo_h_deviation fm_wo_h_deviation_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_wo_h_deviation
    ADD CONSTRAINT fm_wo_h_deviation_pkey PRIMARY KEY (workorder_id, hour_id, id);


--
-- Name: fm_wo_hours_category fm_wo_hours_category_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_wo_hours_category
    ADD CONSTRAINT fm_wo_hours_category_pkey PRIMARY KEY (id);


--
-- Name: fm_wo_hours fm_wo_hours_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_wo_hours
    ADD CONSTRAINT fm_wo_hours_pkey PRIMARY KEY (id);


--
-- Name: fm_workorder_budget fm_workorder_budget_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_workorder_budget
    ADD CONSTRAINT fm_workorder_budget_pkey PRIMARY KEY (order_id, year, month);


--
-- Name: fm_workorder_history fm_workorder_history_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_workorder_history
    ADD CONSTRAINT fm_workorder_history_pkey PRIMARY KEY (history_id);


--
-- Name: fm_workorder fm_workorder_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_workorder
    ADD CONSTRAINT fm_workorder_pkey PRIMARY KEY (id);


--
-- Name: fm_workorder_status fm_workorder_status_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_workorder_status
    ADD CONSTRAINT fm_workorder_status_pkey PRIMARY KEY (id);


--
-- Name: phpgw_account_delegates phpgw_account_delegates_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_account_delegates
    ADD CONSTRAINT phpgw_account_delegates_pkey PRIMARY KEY (delegate_id);


--
-- Name: phpgw_accounts phpgw_accounts_account_lid_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_accounts
    ADD CONSTRAINT phpgw_accounts_account_lid_key UNIQUE (account_lid);


--
-- Name: phpgw_accounts_data phpgw_accounts_data_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_accounts_data
    ADD CONSTRAINT phpgw_accounts_data_pkey PRIMARY KEY (account_id);


--
-- Name: phpgw_accounts phpgw_accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_accounts
    ADD CONSTRAINT phpgw_accounts_pkey PRIMARY KEY (account_id);


--
-- Name: phpgw_applications phpgw_applications_app_name_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_applications
    ADD CONSTRAINT phpgw_applications_app_name_key UNIQUE (app_name);


--
-- Name: phpgw_applications phpgw_applications_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_applications
    ADD CONSTRAINT phpgw_applications_pkey PRIMARY KEY (app_id);


--
-- Name: phpgw_async phpgw_async_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_async
    ADD CONSTRAINT phpgw_async_pkey PRIMARY KEY (id);


--
-- Name: phpgw_cache_user phpgw_cache_user_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_cache_user
    ADD CONSTRAINT phpgw_cache_user_pkey PRIMARY KEY (item_key, user_id);


--
-- Name: phpgw_categories phpgw_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_categories
    ADD CONSTRAINT phpgw_categories_pkey PRIMARY KEY (cat_id);


--
-- Name: phpgw_config2_attrib phpgw_config2_attrib_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_config2_attrib
    ADD CONSTRAINT phpgw_config2_attrib_pkey PRIMARY KEY (section_id, id);


--
-- Name: phpgw_config2_choice phpgw_config2_choice_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_config2_choice
    ADD CONSTRAINT phpgw_config2_choice_pkey PRIMARY KEY (section_id, attrib_id, id);


--
-- Name: phpgw_config2_choice phpgw_config2_choice_section_id_attrib_id_value_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_config2_choice
    ADD CONSTRAINT phpgw_config2_choice_section_id_attrib_id_value_key UNIQUE (section_id, attrib_id, value);


--
-- Name: phpgw_config2_section phpgw_config2_section_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_config2_section
    ADD CONSTRAINT phpgw_config2_section_pkey PRIMARY KEY (id);


--
-- Name: phpgw_config2_value phpgw_config2_value_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_config2_value
    ADD CONSTRAINT phpgw_config2_value_pkey PRIMARY KEY (section_id, attrib_id, id);


--
-- Name: phpgw_config phpgw_config_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_config
    ADD CONSTRAINT phpgw_config_pkey PRIMARY KEY (config_app, config_name);


--
-- Name: phpgw_contact_addr phpgw_contact_addr_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_addr
    ADD CONSTRAINT phpgw_contact_addr_pkey PRIMARY KEY (contact_addr_id);


--
-- Name: phpgw_contact_addr_type phpgw_contact_addr_type_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_addr_type
    ADD CONSTRAINT phpgw_contact_addr_type_pkey PRIMARY KEY (addr_type_id);


--
-- Name: phpgw_contact_comm_descr phpgw_contact_comm_descr_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_comm_descr
    ADD CONSTRAINT phpgw_contact_comm_descr_pkey PRIMARY KEY (comm_descr_id);


--
-- Name: phpgw_contact_comm phpgw_contact_comm_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_comm
    ADD CONSTRAINT phpgw_contact_comm_pkey PRIMARY KEY (comm_id);


--
-- Name: phpgw_contact_comm_type phpgw_contact_comm_type_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_comm_type
    ADD CONSTRAINT phpgw_contact_comm_type_pkey PRIMARY KEY (comm_type_id);


--
-- Name: phpgw_contact_note phpgw_contact_note_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_note
    ADD CONSTRAINT phpgw_contact_note_pkey PRIMARY KEY (contact_note_id);


--
-- Name: phpgw_contact_note_type phpgw_contact_note_type_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_note_type
    ADD CONSTRAINT phpgw_contact_note_type_pkey PRIMARY KEY (note_type_id);


--
-- Name: phpgw_contact_org_person phpgw_contact_org_person_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_org_person
    ADD CONSTRAINT phpgw_contact_org_person_pkey PRIMARY KEY (org_id, person_id);


--
-- Name: phpgw_contact_others phpgw_contact_others_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_others
    ADD CONSTRAINT phpgw_contact_others_pkey PRIMARY KEY (other_id);


--
-- Name: phpgw_contact_person phpgw_contact_person_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_person
    ADD CONSTRAINT phpgw_contact_person_pkey PRIMARY KEY (person_id);


--
-- Name: phpgw_contact phpgw_contact_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact
    ADD CONSTRAINT phpgw_contact_pkey PRIMARY KEY (contact_id);


--
-- Name: phpgw_contact_types phpgw_contact_types_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_contact_types
    ADD CONSTRAINT phpgw_contact_types_pkey PRIMARY KEY (contact_type_id);


--
-- Name: phpgw_cust_attribute_group phpgw_cust_attribute_group_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_cust_attribute_group
    ADD CONSTRAINT phpgw_cust_attribute_group_pkey PRIMARY KEY (location_id, id);


--
-- Name: phpgw_cust_attribute phpgw_cust_attribute_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_cust_attribute
    ADD CONSTRAINT phpgw_cust_attribute_pkey PRIMARY KEY (location_id, id);


--
-- Name: phpgw_cust_choice phpgw_cust_choice_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_cust_choice
    ADD CONSTRAINT phpgw_cust_choice_pkey PRIMARY KEY (location_id, attrib_id, id);


--
-- Name: phpgw_cust_function phpgw_cust_function_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_cust_function
    ADD CONSTRAINT phpgw_cust_function_pkey PRIMARY KEY (location_id, id);


--
-- Name: phpgw_group_map phpgw_group_map_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_group_map
    ADD CONSTRAINT phpgw_group_map_pkey PRIMARY KEY (group_id, account_id);


--
-- Name: phpgw_history_log phpgw_history_log_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_history_log
    ADD CONSTRAINT phpgw_history_log_pkey PRIMARY KEY (history_id);


--
-- Name: phpgw_hooks phpgw_hooks_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_hooks
    ADD CONSTRAINT phpgw_hooks_pkey PRIMARY KEY (hook_id);


--
-- Name: phpgw_interlink phpgw_interlink_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_interlink
    ADD CONSTRAINT phpgw_interlink_pkey PRIMARY KEY (interlink_id);


--
-- Name: phpgw_interserv phpgw_interserv_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_interserv
    ADD CONSTRAINT phpgw_interserv_pkey PRIMARY KEY (server_id);


--
-- Name: phpgw_lang phpgw_lang_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_lang
    ADD CONSTRAINT phpgw_lang_pkey PRIMARY KEY (message_id, app_name, lang);


--
-- Name: phpgw_languages phpgw_languages_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_languages
    ADD CONSTRAINT phpgw_languages_pkey PRIMARY KEY (lang_id);


--
-- Name: phpgw_locations phpgw_locations_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_locations
    ADD CONSTRAINT phpgw_locations_pkey PRIMARY KEY (location_id);


--
-- Name: phpgw_log phpgw_log_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_log
    ADD CONSTRAINT phpgw_log_pkey PRIMARY KEY (log_id);


--
-- Name: phpgw_mail_handler phpgw_mail_handler_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_mail_handler
    ADD CONSTRAINT phpgw_mail_handler_pkey PRIMARY KEY (handler_id);


--
-- Name: phpgw_mapping phpgw_mapping_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_mapping
    ADD CONSTRAINT phpgw_mapping_pkey PRIMARY KEY (ext_user, location, auth_type);


--
-- Name: phpgw_nextid phpgw_nextid_appname_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_nextid
    ADD CONSTRAINT phpgw_nextid_appname_key UNIQUE (appname);


--
-- Name: phpgw_notification phpgw_notification_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_notification
    ADD CONSTRAINT phpgw_notification_pkey PRIMARY KEY (id);


--
-- Name: phpgw_preferences phpgw_preferences_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_preferences
    ADD CONSTRAINT phpgw_preferences_pkey PRIMARY KEY (preference_owner, preference_app);


--
-- Name: phpgw_sessions phpgw_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_sessions
    ADD CONSTRAINT phpgw_sessions_pkey PRIMARY KEY (session_id);


--
-- Name: phpgw_vfs_file_relation phpgw_vfs_file_relation_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_vfs_file_relation
    ADD CONSTRAINT phpgw_vfs_file_relation_pkey PRIMARY KEY (relation_id);


--
-- Name: phpgw_vfs_filedata phpgw_vfs_filedata_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_vfs_filedata
    ADD CONSTRAINT phpgw_vfs_filedata_pkey PRIMARY KEY (file_id);


--
-- Name: phpgw_vfs phpgw_vfs_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_vfs
    ADD CONSTRAINT phpgw_vfs_pkey PRIMARY KEY (file_id);


--
-- Name: rental_adjustment rental_adjustment_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_adjustment
    ADD CONSTRAINT rental_adjustment_pkey PRIMARY KEY (id);


--
-- Name: rental_application_comment rental_application_comment_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_application_comment
    ADD CONSTRAINT rental_application_comment_pkey PRIMARY KEY (id);


--
-- Name: rental_application_composite rental_application_composite_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_application_composite
    ADD CONSTRAINT rental_application_composite_pkey PRIMARY KEY (id);


--
-- Name: rental_application rental_application_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_application
    ADD CONSTRAINT rental_application_pkey PRIMARY KEY (id);


--
-- Name: rental_billing_info rental_billing_info_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_billing_info
    ADD CONSTRAINT rental_billing_info_pkey PRIMARY KEY (id);


--
-- Name: rental_billing rental_billing_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_billing
    ADD CONSTRAINT rental_billing_pkey PRIMARY KEY (id);


--
-- Name: rental_billing_term rental_billing_term_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_billing_term
    ADD CONSTRAINT rental_billing_term_pkey PRIMARY KEY (id);


--
-- Name: rental_composite rental_composite_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_composite
    ADD CONSTRAINT rental_composite_pkey PRIMARY KEY (id);


--
-- Name: rental_composite_standard rental_composite_standard_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_composite_standard
    ADD CONSTRAINT rental_composite_standard_pkey PRIMARY KEY (id);


--
-- Name: rental_composite_type rental_composite_type_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_composite_type
    ADD CONSTRAINT rental_composite_type_pkey PRIMARY KEY (id);


--
-- Name: rental_contract_composite rental_contract_composite_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_composite
    ADD CONSTRAINT rental_contract_composite_pkey PRIMARY KEY (id);


--
-- Name: rental_contract_last_edited rental_contract_last_edited_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_last_edited
    ADD CONSTRAINT rental_contract_last_edited_pkey PRIMARY KEY (contract_id, account_id);


--
-- Name: rental_contract_party rental_contract_party_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_party
    ADD CONSTRAINT rental_contract_party_pkey PRIMARY KEY (contract_id, party_id);


--
-- Name: rental_contract rental_contract_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract
    ADD CONSTRAINT rental_contract_pkey PRIMARY KEY (id);


--
-- Name: rental_contract_price_item rental_contract_price_item_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_price_item
    ADD CONSTRAINT rental_contract_price_item_pkey PRIMARY KEY (id);


--
-- Name: rental_contract_responsibility rental_contract_responsibility_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_responsibility
    ADD CONSTRAINT rental_contract_responsibility_pkey PRIMARY KEY (id);


--
-- Name: rental_contract_responsibility_unit rental_contract_responsibility_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_responsibility_unit
    ADD CONSTRAINT rental_contract_responsibility_unit_pkey PRIMARY KEY (id);


--
-- Name: rental_contract_types rental_contract_types_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_types
    ADD CONSTRAINT rental_contract_types_pkey PRIMARY KEY (id);


--
-- Name: rental_document rental_document_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_document
    ADD CONSTRAINT rental_document_pkey PRIMARY KEY (id);


--
-- Name: rental_document_types rental_document_types_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_document_types
    ADD CONSTRAINT rental_document_types_pkey PRIMARY KEY (id);


--
-- Name: rental_email_out_party rental_email_out_party_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_email_out_party
    ADD CONSTRAINT rental_email_out_party_pkey PRIMARY KEY (id);


--
-- Name: rental_email_out rental_email_out_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_email_out
    ADD CONSTRAINT rental_email_out_pkey PRIMARY KEY (id);


--
-- Name: rental_email_template rental_email_template_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_email_template
    ADD CONSTRAINT rental_email_template_pkey PRIMARY KEY (id);


--
-- Name: rental_invoice rental_invoice_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_invoice
    ADD CONSTRAINT rental_invoice_pkey PRIMARY KEY (id);


--
-- Name: rental_invoice_price_item rental_invoice_price_item_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_invoice_price_item
    ADD CONSTRAINT rental_invoice_price_item_pkey PRIMARY KEY (id);


--
-- Name: rental_location_factor rental_location_factor_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_location_factor
    ADD CONSTRAINT rental_location_factor_pkey PRIMARY KEY (id);


--
-- Name: rental_movein_comment rental_movein_comment_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_movein_comment
    ADD CONSTRAINT rental_movein_comment_pkey PRIMARY KEY (id);


--
-- Name: rental_movein rental_movein_contract_id_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_movein
    ADD CONSTRAINT rental_movein_contract_id_key UNIQUE (contract_id);


--
-- Name: rental_movein rental_movein_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_movein
    ADD CONSTRAINT rental_movein_pkey PRIMARY KEY (id);


--
-- Name: rental_moveout_comment rental_moveout_comment_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_moveout_comment
    ADD CONSTRAINT rental_moveout_comment_pkey PRIMARY KEY (id);


--
-- Name: rental_moveout rental_moveout_contract_id_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_moveout
    ADD CONSTRAINT rental_moveout_contract_id_key UNIQUE (contract_id);


--
-- Name: rental_moveout rental_moveout_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_moveout
    ADD CONSTRAINT rental_moveout_pkey PRIMARY KEY (id);


--
-- Name: rental_notification rental_notification_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_notification
    ADD CONSTRAINT rental_notification_pkey PRIMARY KEY (id);


--
-- Name: rental_notification_workbench rental_notification_workbench_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_notification_workbench
    ADD CONSTRAINT rental_notification_workbench_pkey PRIMARY KEY (id);


--
-- Name: rental_party rental_party_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_party
    ADD CONSTRAINT rental_party_pkey PRIMARY KEY (id);


--
-- Name: rental_price_item rental_price_item_agresso_id_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_price_item
    ADD CONSTRAINT rental_price_item_agresso_id_key UNIQUE (agresso_id);


--
-- Name: rental_price_item rental_price_item_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_price_item
    ADD CONSTRAINT rental_price_item_pkey PRIMARY KEY (id);


--
-- Name: rental_unit rental_unit_composite_id_location_code_key; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_unit
    ADD CONSTRAINT rental_unit_composite_id_location_code_key UNIQUE (composite_id, location_code);


--
-- Name: rental_unit rental_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_unit
    ADD CONSTRAINT rental_unit_pkey PRIMARY KEY (id);


--
-- Name: access_phpgw_contact_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX access_phpgw_contact_idx ON public.phpgw_contact USING btree (access);


--
-- Name: acl_account_phpgw_acl_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX acl_account_phpgw_acl_idx ON public.phpgw_acl USING btree (acl_account);


--
-- Name: active_phpgw_contact_comm_type_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX active_phpgw_contact_comm_type_idx ON public.phpgw_contact_comm_type USING btree (active);


--
-- Name: active_phpgw_contact_org_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX active_phpgw_contact_org_idx ON public.phpgw_contact_org USING btree (active);


--
-- Name: addr_id_phpgw_contact_org_person_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX addr_id_phpgw_contact_org_person_idx ON public.phpgw_contact_org_person USING btree (addr_id);


--
-- Name: addr_type_id_phpgw_contact_addr_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX addr_type_id_phpgw_contact_addr_idx ON public.phpgw_contact_addr USING btree (addr_type_id);


--
-- Name: app_id_phpgw_locations_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX app_id_phpgw_locations_idx ON public.phpgw_locations USING btree (app_id);


--
-- Name: class_phpgw_contact_comm_type_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX class_phpgw_contact_comm_type_idx ON public.phpgw_contact_comm_type USING btree (class);


--
-- Name: comm_data_phpgw_contact_comm_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX comm_data_phpgw_contact_comm_idx ON public.phpgw_contact_comm USING btree (comm_data);


--
-- Name: comm_descr_id_phpgw_contact_comm_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX comm_descr_id_phpgw_contact_comm_idx ON public.phpgw_contact_comm USING btree (comm_descr_id);


--
-- Name: comm_type_id_phpgw_contact_comm_descr_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX comm_type_id_phpgw_contact_comm_descr_idx ON public.phpgw_contact_comm_descr USING btree (comm_type_id);


--
-- Name: contact_id_phpgw_contact_addr_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX contact_id_phpgw_contact_addr_idx ON public.phpgw_contact_addr USING btree (contact_id);


--
-- Name: contact_id_phpgw_contact_comm_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX contact_id_phpgw_contact_comm_idx ON public.phpgw_contact_comm USING btree (contact_id);


--
-- Name: contact_id_phpgw_contact_note_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX contact_id_phpgw_contact_note_idx ON public.phpgw_contact_note USING btree (contact_id);


--
-- Name: contact_id_phpgw_contact_others_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX contact_id_phpgw_contact_others_idx ON public.phpgw_contact_others USING btree (contact_id);


--
-- Name: contact_owner_phpgw_contact_others_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX contact_owner_phpgw_contact_others_idx ON public.phpgw_contact_others USING btree (contact_owner);


--
-- Name: contact_type_descr_phpgw_contact_types_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX contact_type_descr_phpgw_contact_types_idx ON public.phpgw_contact_types USING btree (contact_type_descr);


--
-- Name: contact_type_id_phpgw_contact_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX contact_type_id_phpgw_contact_idx ON public.phpgw_contact USING btree (contact_type_id);


--
-- Name: descr_phpgw_contact_comm_descr_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX descr_phpgw_contact_comm_descr_idx ON public.phpgw_contact_comm_descr USING btree (descr);


--
-- Name: entity_id_fm_investment_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX entity_id_fm_investment_idx ON public.fm_investment USING btree (entity_id);


--
-- Name: first_name_phpgw_contact_person_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX first_name_phpgw_contact_person_idx ON public.phpgw_contact_person USING btree (first_name);


--
-- Name: is_active_phpgw_mail_handler_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX is_active_phpgw_mail_handler_idx ON public.phpgw_mail_handler USING btree (is_active);


--
-- Name: last_name_phpgw_contact_person_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX last_name_phpgw_contact_person_idx ON public.phpgw_contact_person USING btree (last_name);


--
-- Name: lastmodts_phpgw_cache_user_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX lastmodts_phpgw_cache_user_idx ON public.phpgw_cache_user USING btree (lastmodts);


--
-- Name: lastmodts_phpgw_sessions_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX lastmodts_phpgw_sessions_idx ON public.phpgw_sessions USING btree (lastmodts);


--
-- Name: location_code_fm_document_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_document_idx ON public.fm_document USING btree (location_code);


--
-- Name: location_code_fm_gab_location_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_gab_location_idx ON public.fm_gab_location USING btree (location_code);


--
-- Name: location_code_fm_investment_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_investment_idx ON public.fm_investment USING btree (location_code);


--
-- Name: location_code_fm_location1_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_location1_idx ON public.fm_location1 USING btree (location_code);


--
-- Name: location_code_fm_location2_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_location2_idx ON public.fm_location2 USING btree (location_code);


--
-- Name: location_code_fm_location3_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_location3_idx ON public.fm_location3 USING btree (location_code);


--
-- Name: location_code_fm_location4_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_location4_idx ON public.fm_location4 USING btree (location_code);


--
-- Name: location_code_fm_project_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_project_idx ON public.fm_project USING btree (location_code);


--
-- Name: location_code_fm_request_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_request_idx ON public.fm_request USING btree (location_code);


--
-- Name: location_code_fm_responsibility_contact_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_responsibility_contact_idx ON public.fm_responsibility_contact USING btree (location_code);


--
-- Name: location_code_fm_tts_tickets_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_code_fm_tts_tickets_idx ON public.fm_tts_tickets USING btree (location_code);


--
-- Name: location_id_phpgw_acl_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX location_id_phpgw_acl_idx ON public.phpgw_acl USING btree (location_id);


--
-- Name: name_phpgw_locations_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX name_phpgw_locations_idx ON public.phpgw_locations USING btree (name);


--
-- Name: note_type_id_phpgw_contact_note_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX note_type_id_phpgw_contact_note_idx ON public.phpgw_contact_note USING btree (note_type_id);


--
-- Name: org_id_phpgw_contact_org_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX org_id_phpgw_contact_org_idx ON public.phpgw_contact_org USING btree (org_id);


--
-- Name: org_id_phpgw_contact_org_person_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX org_id_phpgw_contact_org_person_idx ON public.phpgw_contact_org_person USING btree (org_id);


--
-- Name: other_name_phpgw_contact_others_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX other_name_phpgw_contact_others_idx ON public.phpgw_contact_others USING btree (other_name);


--
-- Name: owner_phpgw_contact_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX owner_phpgw_contact_idx ON public.phpgw_contact USING btree (owner);


--
-- Name: person_id_phpgw_contact_org_person_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX person_id_phpgw_contact_org_person_idx ON public.phpgw_contact_org_person USING btree (person_id);


--
-- Name: preferred_phpgw_contact_addr_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX preferred_phpgw_contact_addr_idx ON public.phpgw_contact_addr USING btree (preferred);


--
-- Name: preferred_phpgw_contact_comm_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX preferred_phpgw_contact_comm_idx ON public.phpgw_contact_comm USING btree (preferred);


--
-- Name: preferred_phpgw_contact_org_person_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX preferred_phpgw_contact_org_person_idx ON public.phpgw_contact_org_person USING btree (preferred);


--
-- Name: target_email_phpgw_mail_handler_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX target_email_phpgw_mail_handler_idx ON public.phpgw_mail_handler USING btree (target_email);


--
-- Name: type_phpgw_contact_comm_type_idx; Type: INDEX; Schema: public; Owner: portico
--

CREATE INDEX type_phpgw_contact_comm_type_idx ON public.phpgw_contact_comm_type USING btree (type);


--
-- Name: controller_check_item_case controller_check_item_case_check_item_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_check_item_case
    ADD CONSTRAINT controller_check_item_case_check_item_id_fkey FOREIGN KEY (check_item_id) REFERENCES public.controller_check_item(id);


--
-- Name: controller_control_serie_history controller_control_serie_history_serie_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_control_serie_history
    ADD CONSTRAINT controller_control_serie_history_serie_id_fkey FOREIGN KEY (serie_id) REFERENCES public.controller_control_serie(id);


--
-- Name: controller_document controller_document_procedure_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_document
    ADD CONSTRAINT controller_document_procedure_id_fkey FOREIGN KEY (procedure_id) REFERENCES public.controller_procedure(id);


--
-- Name: controller_document controller_document_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.controller_document
    ADD CONSTRAINT controller_document_type_id_fkey FOREIGN KEY (type_id) REFERENCES public.controller_document_types(id);


--
-- Name: fm_document_relation fm_document_relation_document_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_document_relation
    ADD CONSTRAINT fm_document_relation_document_id_fkey FOREIGN KEY (document_id) REFERENCES public.fm_document(id);


--
-- Name: fm_eco_periodization_outline fm_eco_periodization_outline_periodization_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_eco_periodization_outline
    ADD CONSTRAINT fm_eco_periodization_outline_periodization_id_fkey FOREIGN KEY (periodization_id) REFERENCES public.fm_eco_periodization(id);


--
-- Name: fm_ecodimb fm_ecodimb_org_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecodimb
    ADD CONSTRAINT fm_ecodimb_org_unit_id_fkey FOREIGN KEY (org_unit_id) REFERENCES public.fm_org_unit(id);


--
-- Name: fm_ecodimb_role_user fm_ecodimb_role_user_ecodimb_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecodimb_role_user
    ADD CONSTRAINT fm_ecodimb_role_user_ecodimb_fkey FOREIGN KEY (ecodimb) REFERENCES public.fm_ecodimb(id);


--
-- Name: fm_ecodimb_role_user fm_ecodimb_role_user_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecodimb_role_user
    ADD CONSTRAINT fm_ecodimb_role_user_role_id_fkey FOREIGN KEY (role_id) REFERENCES public.fm_ecodimb_role(id);


--
-- Name: fm_ecodimb_role_user fm_ecodimb_role_user_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_ecodimb_role_user
    ADD CONSTRAINT fm_ecodimb_role_user_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.phpgw_accounts(account_id);


--
-- Name: fm_jasper_input fm_jasper_input_input_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_jasper_input
    ADD CONSTRAINT fm_jasper_input_input_type_id_fkey FOREIGN KEY (input_type_id) REFERENCES public.fm_jasper_input_type(id);


--
-- Name: fm_jasper_input fm_jasper_input_jasper_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_jasper_input
    ADD CONSTRAINT fm_jasper_input_jasper_id_fkey FOREIGN KEY (jasper_id) REFERENCES public.fm_jasper(id);


--
-- Name: fm_location1 fm_location1_category_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location1
    ADD CONSTRAINT fm_location1_category_fkey FOREIGN KEY (category) REFERENCES public.fm_location1_category(id);


--
-- Name: fm_location2 fm_location2_category_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location2
    ADD CONSTRAINT fm_location2_category_fkey FOREIGN KEY (category) REFERENCES public.fm_location2_category(id);


--
-- Name: fm_location2 fm_location2_loc1_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location2
    ADD CONSTRAINT fm_location2_loc1_fkey FOREIGN KEY (loc1) REFERENCES public.fm_location1(loc1);


--
-- Name: fm_location3 fm_location3_category_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location3
    ADD CONSTRAINT fm_location3_category_fkey FOREIGN KEY (category) REFERENCES public.fm_location3_category(id);


--
-- Name: fm_location3 fm_location3_loc1_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location3
    ADD CONSTRAINT fm_location3_loc1_fkey FOREIGN KEY (loc1, loc2) REFERENCES public.fm_location2(loc1, loc2);


--
-- Name: fm_location4 fm_location4_category_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location4
    ADD CONSTRAINT fm_location4_category_fkey FOREIGN KEY (category) REFERENCES public.fm_location4_category(id);


--
-- Name: fm_location4 fm_location4_loc1_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location4
    ADD CONSTRAINT fm_location4_loc1_fkey FOREIGN KEY (loc1, loc2, loc3) REFERENCES public.fm_location3(loc1, loc2, loc3);


--
-- Name: fm_location_contact fm_location_contact_contact_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_contact
    ADD CONSTRAINT fm_location_contact_contact_id_fkey FOREIGN KEY (contact_id) REFERENCES public.phpgw_contact(contact_id);


--
-- Name: fm_location_contact fm_location_contact_location_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_contact
    ADD CONSTRAINT fm_location_contact_location_code_fkey FOREIGN KEY (location_code) REFERENCES public.fm_locations(location_code);


--
-- Name: fm_location_exception fm_location_exception_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_exception
    ADD CONSTRAINT fm_location_exception_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.fm_location_exception_category(id);


--
-- Name: fm_location_exception_category_text fm_location_exception_category_text_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_exception_category_text
    ADD CONSTRAINT fm_location_exception_category_text_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.fm_location_exception_category(id);


--
-- Name: fm_location_exception fm_location_exception_severity_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_location_exception
    ADD CONSTRAINT fm_location_exception_severity_id_fkey FOREIGN KEY (severity_id) REFERENCES public.fm_location_exception_severity(id);


--
-- Name: fm_part_of_town fm_part_of_town_district_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_part_of_town
    ADD CONSTRAINT fm_part_of_town_district_id_fkey FOREIGN KEY (district_id) REFERENCES public.fm_district(id);


--
-- Name: fm_project_budget fm_project_budget_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_project_budget
    ADD CONSTRAINT fm_project_budget_project_id_fkey FOREIGN KEY (project_id) REFERENCES public.fm_project(id);


--
-- Name: fm_request_consume fm_request_consume_request_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request_consume
    ADD CONSTRAINT fm_request_consume_request_id_fkey FOREIGN KEY (request_id) REFERENCES public.fm_request(id);


--
-- Name: fm_request_planning fm_request_planning_request_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_request_planning
    ADD CONSTRAINT fm_request_planning_request_id_fkey FOREIGN KEY (request_id) REFERENCES public.fm_request(id);


--
-- Name: fm_responsibility_contact fm_responsibility_contact_contact_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility_contact
    ADD CONSTRAINT fm_responsibility_contact_contact_id_fkey FOREIGN KEY (contact_id) REFERENCES public.phpgw_contact(contact_id);


--
-- Name: fm_responsibility_contact fm_responsibility_contact_responsibility_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility_contact
    ADD CONSTRAINT fm_responsibility_contact_responsibility_role_id_fkey FOREIGN KEY (responsibility_role_id) REFERENCES public.fm_responsibility_role(id);


--
-- Name: fm_responsibility_module fm_responsibility_module_cat_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility_module
    ADD CONSTRAINT fm_responsibility_module_cat_id_fkey FOREIGN KEY (cat_id) REFERENCES public.phpgw_categories(cat_id);


--
-- Name: fm_responsibility_module fm_responsibility_module_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility_module
    ADD CONSTRAINT fm_responsibility_module_location_id_fkey FOREIGN KEY (location_id) REFERENCES public.phpgw_locations(location_id);


--
-- Name: fm_responsibility_module fm_responsibility_module_responsibility_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility_module
    ADD CONSTRAINT fm_responsibility_module_responsibility_id_fkey FOREIGN KEY (responsibility_id) REFERENCES public.fm_responsibility(id);


--
-- Name: fm_responsibility_role fm_responsibility_role_responsibility_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_responsibility_role
    ADD CONSTRAINT fm_responsibility_role_responsibility_id_fkey FOREIGN KEY (responsibility_id) REFERENCES public.fm_responsibility(id);


--
-- Name: fm_tts_budget fm_tts_budget_ticket_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tts_budget
    ADD CONSTRAINT fm_tts_budget_ticket_id_fkey FOREIGN KEY (ticket_id) REFERENCES public.fm_tts_tickets(id);


--
-- Name: fm_tts_payments fm_tts_payments_ticket_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_tts_payments
    ADD CONSTRAINT fm_tts_payments_ticket_id_fkey FOREIGN KEY (ticket_id) REFERENCES public.fm_tts_tickets(id);


--
-- Name: fm_view_dataset_report fm_view_dataset_report_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_view_dataset_report
    ADD CONSTRAINT fm_view_dataset_report_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.fm_view_dataset(id);


--
-- Name: fm_workorder_budget fm_workorder_budget_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.fm_workorder_budget
    ADD CONSTRAINT fm_workorder_budget_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.fm_workorder(id);


--
-- Name: phpgw_accounts_data phpgw_accounts_data_account_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_accounts_data
    ADD CONSTRAINT phpgw_accounts_data_account_id_fkey FOREIGN KEY (account_id) REFERENCES public.phpgw_accounts(account_id);


--
-- Name: phpgw_notification phpgw_notification_contact_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_notification
    ADD CONSTRAINT phpgw_notification_contact_id_fkey FOREIGN KEY (contact_id) REFERENCES public.phpgw_contact(contact_id);


--
-- Name: phpgw_vfs_file_relation phpgw_vfs_file_relation_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_vfs_file_relation
    ADD CONSTRAINT phpgw_vfs_file_relation_file_id_fkey FOREIGN KEY (file_id) REFERENCES public.phpgw_vfs(file_id);


--
-- Name: phpgw_vfs_filedata phpgw_vfs_filedata_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.phpgw_vfs_filedata
    ADD CONSTRAINT phpgw_vfs_filedata_file_id_fkey FOREIGN KEY (file_id) REFERENCES public.phpgw_vfs(file_id);


--
-- Name: rental_application_comment rental_application_comment_application_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_application_comment
    ADD CONSTRAINT rental_application_comment_application_id_fkey FOREIGN KEY (application_id) REFERENCES public.rental_application(id);


--
-- Name: rental_application_composite rental_application_composite_application_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_application_composite
    ADD CONSTRAINT rental_application_composite_application_id_fkey FOREIGN KEY (application_id) REFERENCES public.rental_application(id);


--
-- Name: rental_application_composite rental_application_composite_composite_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_application_composite
    ADD CONSTRAINT rental_application_composite_composite_id_fkey FOREIGN KEY (composite_id) REFERENCES public.rental_composite(id);


--
-- Name: rental_billing rental_billing_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_billing
    ADD CONSTRAINT rental_billing_created_by_fkey FOREIGN KEY (created_by) REFERENCES public.phpgw_accounts(account_id);


--
-- Name: rental_billing_info rental_billing_info_billing_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_billing_info
    ADD CONSTRAINT rental_billing_info_billing_id_fkey FOREIGN KEY (billing_id) REFERENCES public.rental_billing(id);


--
-- Name: rental_billing rental_billing_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_billing
    ADD CONSTRAINT rental_billing_location_id_fkey FOREIGN KEY (location_id) REFERENCES public.phpgw_locations(location_id);


--
-- Name: rental_contract_composite rental_contract_composite_composite_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_composite
    ADD CONSTRAINT rental_contract_composite_composite_id_fkey FOREIGN KEY (composite_id) REFERENCES public.rental_composite(id);


--
-- Name: rental_contract_composite rental_contract_composite_contract_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_composite
    ADD CONSTRAINT rental_contract_composite_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES public.rental_contract(id);


--
-- Name: rental_contract rental_contract_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract
    ADD CONSTRAINT rental_contract_created_by_fkey FOREIGN KEY (created_by) REFERENCES public.phpgw_accounts(account_id);


--
-- Name: rental_contract_last_edited rental_contract_last_edited_account_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_last_edited
    ADD CONSTRAINT rental_contract_last_edited_account_id_fkey FOREIGN KEY (account_id) REFERENCES public.phpgw_accounts(account_id);


--
-- Name: rental_contract_last_edited rental_contract_last_edited_contract_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_last_edited
    ADD CONSTRAINT rental_contract_last_edited_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES public.rental_contract(id);


--
-- Name: rental_contract rental_contract_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract
    ADD CONSTRAINT rental_contract_location_id_fkey FOREIGN KEY (location_id) REFERENCES public.phpgw_locations(location_id);


--
-- Name: rental_contract_party rental_contract_party_contract_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_party
    ADD CONSTRAINT rental_contract_party_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES public.rental_contract(id);


--
-- Name: rental_contract_party rental_contract_party_party_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_party
    ADD CONSTRAINT rental_contract_party_party_id_fkey FOREIGN KEY (party_id) REFERENCES public.rental_party(id);


--
-- Name: rental_contract_price_item rental_contract_price_item_contract_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_price_item
    ADD CONSTRAINT rental_contract_price_item_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES public.rental_contract(id);


--
-- Name: rental_contract_price_item rental_contract_price_item_price_item_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_price_item
    ADD CONSTRAINT rental_contract_price_item_price_item_id_fkey FOREIGN KEY (price_item_id) REFERENCES public.rental_price_item(id);


--
-- Name: rental_contract_responsibility rental_contract_responsibility_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_responsibility
    ADD CONSTRAINT rental_contract_responsibility_location_id_fkey FOREIGN KEY (location_id) REFERENCES public.phpgw_locations(location_id);


--
-- Name: rental_contract rental_contract_term_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract
    ADD CONSTRAINT rental_contract_term_id_fkey FOREIGN KEY (term_id) REFERENCES public.rental_billing_term(id);


--
-- Name: rental_contract_types rental_contract_types_responsibility_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_contract_types
    ADD CONSTRAINT rental_contract_types_responsibility_id_fkey FOREIGN KEY (responsibility_id) REFERENCES public.rental_contract_responsibility(id);


--
-- Name: rental_document rental_document_contract_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_document
    ADD CONSTRAINT rental_document_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES public.rental_contract(id);


--
-- Name: rental_document rental_document_party_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_document
    ADD CONSTRAINT rental_document_party_id_fkey FOREIGN KEY (party_id) REFERENCES public.rental_party(id);


--
-- Name: rental_document rental_document_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_document
    ADD CONSTRAINT rental_document_type_id_fkey FOREIGN KEY (type_id) REFERENCES public.rental_document_types(id);


--
-- Name: rental_email_out_party rental_email_out_party_email_out_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_email_out_party
    ADD CONSTRAINT rental_email_out_party_email_out_id_fkey FOREIGN KEY (email_out_id) REFERENCES public.rental_email_out(id);


--
-- Name: rental_email_out_party rental_email_out_party_party_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_email_out_party
    ADD CONSTRAINT rental_email_out_party_party_id_fkey FOREIGN KEY (party_id) REFERENCES public.rental_party(id);


--
-- Name: rental_invoice rental_invoice_billing_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_invoice
    ADD CONSTRAINT rental_invoice_billing_id_fkey FOREIGN KEY (billing_id) REFERENCES public.rental_billing(id);


--
-- Name: rental_invoice rental_invoice_contract_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_invoice
    ADD CONSTRAINT rental_invoice_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES public.rental_contract(id);


--
-- Name: rental_invoice rental_invoice_party_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_invoice
    ADD CONSTRAINT rental_invoice_party_id_fkey FOREIGN KEY (party_id) REFERENCES public.rental_party(id);


--
-- Name: rental_invoice_price_item rental_invoice_price_item_invoice_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_invoice_price_item
    ADD CONSTRAINT rental_invoice_price_item_invoice_id_fkey FOREIGN KEY (invoice_id) REFERENCES public.rental_invoice(id);


--
-- Name: rental_location_factor rental_location_factor_part_of_town_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_location_factor
    ADD CONSTRAINT rental_location_factor_part_of_town_id_fkey FOREIGN KEY (part_of_town_id) REFERENCES public.fm_part_of_town(id);


--
-- Name: rental_movein rental_movein_account_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_movein
    ADD CONSTRAINT rental_movein_account_id_fkey FOREIGN KEY (account_id) REFERENCES public.phpgw_accounts(account_id);


--
-- Name: rental_movein_comment rental_movein_comment_movein_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_movein_comment
    ADD CONSTRAINT rental_movein_comment_movein_id_fkey FOREIGN KEY (movein_id) REFERENCES public.rental_movein(id);


--
-- Name: rental_movein rental_movein_contract_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_movein
    ADD CONSTRAINT rental_movein_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES public.rental_contract(id);


--
-- Name: rental_moveout rental_moveout_account_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_moveout
    ADD CONSTRAINT rental_moveout_account_id_fkey FOREIGN KEY (account_id) REFERENCES public.phpgw_accounts(account_id);


--
-- Name: rental_moveout_comment rental_moveout_comment_moveout_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_moveout_comment
    ADD CONSTRAINT rental_moveout_comment_moveout_id_fkey FOREIGN KEY (moveout_id) REFERENCES public.rental_moveout(id);


--
-- Name: rental_moveout rental_moveout_contract_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_moveout
    ADD CONSTRAINT rental_moveout_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES public.rental_contract(id);


--
-- Name: rental_notification rental_notification_account_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_notification
    ADD CONSTRAINT rental_notification_account_id_fkey FOREIGN KEY (account_id) REFERENCES public.phpgw_accounts(account_id);


--
-- Name: rental_notification rental_notification_contract_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_notification
    ADD CONSTRAINT rental_notification_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES public.rental_contract(id);


--
-- Name: rental_notification rental_notification_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_notification
    ADD CONSTRAINT rental_notification_location_id_fkey FOREIGN KEY (location_id) REFERENCES public.phpgw_locations(location_id);


--
-- Name: rental_notification_workbench rental_notification_workbench_account_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_notification_workbench
    ADD CONSTRAINT rental_notification_workbench_account_id_fkey FOREIGN KEY (account_id) REFERENCES public.phpgw_accounts(account_id);


--
-- Name: rental_notification_workbench rental_notification_workbench_notification_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_notification_workbench
    ADD CONSTRAINT rental_notification_workbench_notification_id_fkey FOREIGN KEY (notification_id) REFERENCES public.rental_notification(id);


--
-- Name: rental_unit rental_unit_composite_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: portico
--

ALTER TABLE ONLY public.rental_unit
    ADD CONSTRAINT rental_unit_composite_id_fkey FOREIGN KEY (composite_id) REFERENCES public.rental_composite(id);


--
-- PostgreSQL database dump complete
--

