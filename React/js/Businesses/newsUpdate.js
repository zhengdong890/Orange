class CashClass{
	constructor() {
        this.detailsData  = WINDOWS_SHOP;        
    }	
	
	getConfig(){
	    return {
	    	detailsData : this.detailsData 
	    }    	
	}
	
	
	getData(){
		return{				
			detailsData : this.detailsData
	    } 
	}
}
var cash = new CashClass();
export default cash;