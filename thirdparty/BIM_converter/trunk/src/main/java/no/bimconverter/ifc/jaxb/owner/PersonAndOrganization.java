package no.bimconverter.ifc.jaxb.owner;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.SIfc2x3.AIfcactorrole;
import jsdai.SIfc2x3.AIfcaddress;
import jsdai.SIfc2x3.EIfcpersonandorganization;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;

@XmlRootElement
public class PersonAndOrganization {
	private Person person;
	private Organization organization;
	private  ActorRole[] roles;
	
	public PersonAndOrganization() {
	}
	
	public PersonAndOrganization load(EIfcpersonandorganization personAndOrganization) {
		try {
			this.person = new Person().load(personAndOrganization.getTheperson(null));
			this.organization = new Organization().load(personAndOrganization.getTheorganization(null));
			if(personAndOrganization.testRoles(null)) {
				this.roles = getRoleArray(personAndOrganization.getRoles(null));
			}
		} catch (SdaiException e) {
			e.printStackTrace();
		}
		return this;
	}

	protected ActorRole[] getRoleArray(AIfcactorrole actorRoles) throws SdaiException {
		SdaiIterator actorRolesIterator = actorRoles.createIterator();
		List<ActorRole> actorRoleList = new ArrayList<ActorRole>();
		while(actorRolesIterator.next()) {
			actorRoleList.add(new ActorRole().load(actorRoles.getCurrentMember(actorRolesIterator)));
		}
		return actorRoleList.toArray(new ActorRole[actorRoleList.size()]);
	}

	protected Address[] getAddressArray(AIfcaddress actorAddresses) throws SdaiException {
		SdaiIterator actorAddressesIterator = actorAddresses.createIterator();
		List<Address> addressList = new ArrayList<Address>();
		while(actorAddressesIterator.next()) {
			addressList.add(new Address().load(actorAddresses.getCurrentMember(actorAddressesIterator)));
		}
		return addressList.toArray(new Address[addressList.size()]);
	}

	public Person getPerson() {
		return person;
	}

	public void setPerson(Person person) {
		this.person = person;
	}

	public Organization getOrganization() {
		return organization;
	}

	public void setOrganization(Organization organization) {
		this.organization = organization;
	}

	public ActorRole[] getRoles() {
		return roles;
	}

	public void setRoles(ActorRole[] roles) {
		this.roles = roles;
	}

	

}
