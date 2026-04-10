export interface PlanDescriptionItem {
  mainText: string
  subText?: string
}

export interface Plan {
  id: number
  name: string
  priceMonthly: number
  priceMonthlyFull: number
  priceYearly: number
  priceYearlyDiscounted: number
  savings: number
  isFreeTrialAvailable: boolean
  descriptionItems: PlanDescriptionItem[]
}
