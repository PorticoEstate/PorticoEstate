package no.ifc.rest;

import java.util.ArrayList;
import java.util.Collection;
import java.util.List;
import java.util.Vector;

import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.core.GenericEntity;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import javax.xml.bind.annotation.XmlRootElement;

import com.sun.jersey.api.JResponse;
import com.sun.xml.internal.txw2.annotation.XmlElement;

import no.bimfm.ifc.Repositories;
import no.bimfm.ifc.RepositoriesImpl;
import no.bimfm.jaxb.NameValuePair;
import no.bimfm.jaxb.rest.Item;
import no.bimfm.jaxb.rest.RepositoryStatus;
import no.bimfm.jaxb.rest.SimpleList;

@Path("/repositories")
public class RepositoriesRest {
	Repositories repositories = null;
	
	public RepositoriesRest() {
		this.repositories = new RepositoriesImpl();
	}
	@GET
	@Path("count")
	@Produces({MediaType.TEXT_PLAIN,MediaType.TEXT_HTML})
	public String getRepositoryCount() {
		return String.valueOf(this.repositories.getNumberOfRepositories());
	}
	
	@GET
	@Path("count")
	@Produces( { MediaType.APPLICATION_XML, MediaType.APPLICATION_JSON })
	public String getRepositoryCountXml() {
		return String.valueOf(this.repositories.getNumberOfRepositories());
	}
	@GET
	@Path("names")
	@Produces( { MediaType.APPLICATION_XML, MediaType.APPLICATION_JSON })
	public JResponse<List<Item>> getRepositoryNames() {
		List<String> repositoryNames = this.repositories.getRepositoryNames();
		List<Item> result = new ArrayList<Item>();
		for(String name : repositoryNames) {
			result.add(new Item(name));
		}
		return JResponse.ok(result).build();
	}
	@GET
	@Path("status")
	@Produces( { MediaType.APPLICATION_XML, MediaType.APPLICATION_JSON })
	public RepositoryStatus getRepositoryStatus() {
		
		return this.repositories.getRepositoryStatus();
	}
	
	
	@GET
	@Path("test")
	@Produces( { MediaType.APPLICATION_XML, MediaType.APPLICATION_JSON })
	public NameValuePair getTestXml() {
		return new NameValuePair("A variable", "A value");
	}
	
	@GET
	@Produces(MediaType.TEXT_HTML)
	public String defaultReturn() {
		return "This is the repository rest service + Addition4!";
	}
	
	@XmlRootElement(name="myRec")
	private static class ListWrapper {
		
		List<String> list = new ArrayList<String>();
		
		public ListWrapper() {
		}
		public void addString(String s) {list.add(s); }
		
		public List<String> addAll(List<String> list) {
			list.addAll(list);
			return list;
		}
		
		public List<String> getList() {
			return list;
		}
		public void setList(List<String> list) {
			this.list = list;
		}
		
	}
}
