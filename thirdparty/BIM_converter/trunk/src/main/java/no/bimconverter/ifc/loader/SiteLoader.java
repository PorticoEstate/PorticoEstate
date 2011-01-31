package no.bimconverter.ifc.loader;

import java.util.List;

import no.bimconverter.ifc.IfcSdaiException;
import no.bimconverter.ifc.v2x3.object.Site;

import jsdai.SIfc2x3.EIfcsite;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiModel;


public class SiteLoader extends CommonLoader{
	public SiteLoader() {
	}
	
	public Site loadModel(SdaiModel model) {
		List<EEntity> siteList = super.getEntitiesOfType(model, EIfcsite.class);
		Site site = new Site();
		int siteListSize = siteList.size();
		if(siteListSize > 1) {
			throw new IfcSdaiException("Too many projects found!");
		} else if (siteListSize == 1) {
			EIfcsite siteEntity = (EIfcsite) siteList.get(0);
			site.load(siteEntity);
		}
		return site;
	}
}
