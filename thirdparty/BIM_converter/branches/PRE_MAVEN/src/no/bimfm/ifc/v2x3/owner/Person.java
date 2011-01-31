package no.bimfm.ifc.v2x3.owner;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.SIfc2x3.EIfcperson;
import jsdai.lang.A_string;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
/*
 * Utility class to take in information from IFcPerson and use to output via JAXB
 * Could actually just annotate the EIfcPerson class instead of using this, but
 * would be more of a hastle to maintain.
 * 
 * Reference is: IFC 2x3
 */
@XmlRootElement
public class Person extends PersonAndOrganization{
	
	private String id;
	private String familyName;
	private String givenName;
	private String[] middleNames;
	private  String[] prefixTitles;
	private  String[] suffixTitles;
	private  ActorRole[] roles;
	private  Address[] addresses;
	
	public Person() {
	}
	
	public Person load(EIfcperson person) {
		try {
			if(person.testId(null)) {
				this.id = person.getId(null);
			}
			if(person.testFamilyname(null)) {
				this.familyName = person.getFamilyname(null);
			}
			if(person.testGivenname(null)) {
				this.givenName = person.getGivenname(null);
			}
			if(person.testMiddlenames(null)) {
				A_string middleNames = person.getMiddlenames(null);
				this.middleNames = convertAggregateToArray(middleNames);
			}
			if(person.testPrefixtitles(null)) {
				this.prefixTitles = convertAggregateToArray(person.getPrefixtitles(null));
			}
			if(person.testSuffixtitles(null)) {
				this.suffixTitles = convertAggregateToArray(person.getSuffixtitles(null));
			}
			if(person.testRoles(null)) {
				this.roles = getRoleArray(person.getRoles(null));
			}
			if(person.testAddresses(null)) {
				this.addresses = getAddressArray(person.getAddresses(null));
			}
			
		} catch (SdaiException e) {
			e.printStackTrace();
		}
		return this;
		
	}

	

	private String[] convertAggregateToArray(A_string middleNames2) throws SdaiException {
		SdaiIterator stringAggregateIterator = middleNames2.createIterator();
		List<String> output = new ArrayList<String>();
		while(stringAggregateIterator.next()) {
			output.add(middleNames2.getCurrentMember(stringAggregateIterator));
		}
		return output.toArray(new String[output.size()]);
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getFamilyName() {
		return familyName;
	}

	public void setFamilyName(String familyName) {
		this.familyName = familyName;
	}

	public String getGivenName() {
		return givenName;
	}

	public void setGivenName(String givenName) {
		this.givenName = givenName;
	}

	public String[] getMiddleNames() {
		return middleNames;
	}

	public void setMiddleNames(String[] middleNames) {
		this.middleNames = middleNames;
	}

	public String[] getPrefixTitles() {
		return prefixTitles;
	}

	public void setPrefixTitles(String[] prefixTitles) {
		this.prefixTitles = prefixTitles;
	}

	public String[] getSuffixTitles() {
		return suffixTitles;
	}

	public void setSuffixTitles(String[] suffixTitles) {
		this.suffixTitles = suffixTitles;
	}

	public ActorRole[] getRoles() {
		return roles;
	}

	public void setRoles(ActorRole[] roles) {
		this.roles = roles;
	}

	public Address[] getAddresses() {
		return addresses;
	}

	public void setAddresses(Address[] addresses) {
		this.addresses = addresses;
	}
	
}
