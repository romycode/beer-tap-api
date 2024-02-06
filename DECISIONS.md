## DECISIONS

I've installed the module Ramsey UUID to generate UUID v4.

I've decided to create a solution using an Event based architecture because the use case fits very well with this 
approach because of the reactive nature of the workflow.

I tried to put all the logic in the Domain layer and making Application and Infrastructure only an orchestrator
for infrastructure and domain services
